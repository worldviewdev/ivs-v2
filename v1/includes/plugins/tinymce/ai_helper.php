<?php
/**
 * TinyMCE AI Helper
 * Handles AI-powered text generation and improvement
 */
// CORS Headers - Allow requests from localhost and specific domains
$allowed_origins = [
    'http://localhost',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins) || strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(dirname(dirname(__FILE__))) . '/');
}
// Include configuration
require_once ABSPATH . 'midas.inc.php';
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include necessary files
require_once(dirname(__FILE__) . '/../../midas.inc.php');
// Set content type to JSON
header('Content-Type: application/json');
// Check authentication - temporarily disabled for testing
// if (!isset($_SESSION['admin_id']) && !isset($_SESSION['agent_id'])) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Unauthorized', 'success' => false]);
//     exit;
// }
// OpenAI API Configuration
define('OPENAI_API_KEY', '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'); // Replace with actual API key
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
/**
 * Make OpenAI API request
 */
function makeOpenAIRequest($messages, $max_tokens = 1000, $temperature = 0.7) {
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
        'max_tokens' => $max_tokens,
        'temperature' => $temperature,
        'top_p' => 1,
        'frequency_penalty' => 0,
        'presence_penalty' => 0
    ];
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, OPENAI_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) {
        return ['error' => 'CURL Error: ' . $error];
    }
    if ($http_code !== 200) {
        return ['error' => 'HTTP Error: ' . $http_code . ' - ' . $response];
    }
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'JSON Decode Error: ' . json_last_error_msg()];
    }
    return $decoded;
}
/**
 * Generate text based on prompt
 */
function generateText($prompt, $context = '') {
    $messages = [
        [
            'role' => 'system',
            'content' => "You are an expert travel agent specializing in Italy vacations for ItalyVacationSpecialist. You have extensive knowledge of Italian destinations, culture, cuisine, accommodations, and travel logistics. Help create engaging, informative, and professional travel content including itineraries, descriptions, recommendations, and travel advice. Focus on providing authentic Italian experiences, practical travel information, and inspiring content that showcases the beauty and culture of Italy. Always maintain a professional yet enthusiastic tone that reflects the expertise of a seasoned Italy travel specialist."
        ]
    ];
    if (!empty($context)) {
        $messages[] = [
            'role' => 'user',
            'content' => 'Context: ' . $context
        ];
    }
    $messages[] = [
        'role' => 'user',
        'content' => $prompt
    ];
    return makeOpenAIRequest($messages, 800, 0.8);
}
/**
 * Improve existing text
 */
function improveText($text, $instruction = 'improve') {
    $systemPrompt = 'You are a professional editor. ';
    switch ($instruction) {
        case 'improve':
            $systemPrompt .= 'Improve the given text to make it more engaging, clear, and professional while maintaining the original meaning.';
            break;
        case 'shorten':
            $systemPrompt .= 'Make the text more concise while keeping all important information.';
            break;
        case 'expand':
            $systemPrompt .= 'Expand the text with more details, examples, and engaging descriptions.';
            break;
        case 'formal':
            $systemPrompt .= 'Rewrite the text in a more formal, professional tone.';
            break;
        case 'casual':
            $systemPrompt .= 'Rewrite the text in a more casual, friendly tone.';
            break;
        default:
            $systemPrompt .= 'Improve the given text based on the instruction: ' . $instruction;
    }
    $messages = [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ],
        [
            'role' => 'user',
            'content' => $text
        ]
    ];
    return makeOpenAIRequest($messages, 1000, 0.7);
}
/**
 * Translate text
 */
function translateText($text, $targetLanguage) {
    $messages = [
        [
            'role' => 'system',
            'content' => 'You are a professional translator. Translate the given text accurately while maintaining the tone and context.'
        ],
        [
            'role' => 'user',
            'content' => "Translate the following text to {$targetLanguage}:\n\n{$text}"
        ]
    ];
    return makeOpenAIRequest($messages, 1000, 0.3);
}
/**
 * Generate travel itinerary content
 */
function generateItinerary($destination, $activities, $duration = '1 day') {
    $prompt = "Create a detailed travel itinerary for {$destination} for {$duration}. ";
    if (!empty($activities)) {
        $prompt .= "Include these activities: {$activities}. ";
    }
    $prompt .= "Make it engaging and informative with specific recommendations.";
    return generateText($prompt);
}
// Handle API requests
$action = $_GET['action'] ?? $_POST['action'] ?? '';
switch ($action) {
    case 'generate':
        $prompt = $_POST['prompt'] ?? '';
        $context = $_POST['context'] ?? '';
        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt is required', 'success' => false]);
            exit;
        }
        $result = generateText($prompt, $context);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error'], 'success' => false]);
        } else if (isset($result['choices'][0]['message']['content'])) {
            echo json_encode([
                'success' => true,
                'content' => trim($result['choices'][0]['message']['content']),
                'usage' => $result['usage'] ?? null
            ]);
        } else {
            echo json_encode(['error' => 'Unexpected API response', 'success' => false]);
        }
        break;
    case 'improve':
        $text = $_POST['text'] ?? '';
        $instruction = $_POST['instruction'] ?? 'improve';
        if (empty($text)) {
            echo json_encode(['error' => 'Text is required', 'success' => false]);
            exit;
        }
        $result = improveText($text, $instruction);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error'], 'success' => false]);
        } else if (isset($result['choices'][0]['message']['content'])) {
            echo json_encode([
                'success' => true,
                'content' => trim($result['choices'][0]['message']['content']),
                'usage' => $result['usage'] ?? null
            ]);
        } else {
            echo json_encode(['error' => 'Unexpected API response', 'success' => false]);
        }
        break;
    case 'translate':
        $text = $_POST['text'] ?? '';
        $language = $_POST['language'] ?? 'English';
        if (empty($text)) {
            echo json_encode(['error' => 'Text is required', 'success' => false]);
            exit;
        }
        $result = translateText($text, $language);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error'], 'success' => false]);
        } else if (isset($result['choices'][0]['message']['content'])) {
            echo json_encode([
                'success' => true,
                'content' => trim($result['choices'][0]['message']['content']),
                'usage' => $result['usage'] ?? null
            ]);
        } else {
            echo json_encode(['error' => 'Unexpected API response', 'success' => false]);
        }
        break;
    case 'itinerary':
        $destination = $_POST['destination'] ?? '';
        $activities = $_POST['activities'] ?? '';
        $duration = $_POST['duration'] ?? '1 day';
        if (empty($destination)) {
            echo json_encode(['error' => 'Destination is required', 'success' => false]);
            exit;
        }
        $result = generateItinerary($destination, $activities, $duration);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error'], 'success' => false]);
        } else if (isset($result['choices'][0]['message']['content'])) {
            echo json_encode([
                'success' => true,
                'content' => trim($result['choices'][0]['message']['content']),
                'usage' => $result['usage'] ?? null
            ]);
        } else {
            echo json_encode(['error' => 'Unexpected API response', 'success' => false]);
        }
        break;
    case 'test':
        // Test endpoint
        echo json_encode([
            'success' => true,
            'message' => 'AI Helper is working',
            'api_key_set' => !empty(OPENAI_API_KEY) && OPENAI_API_KEY !== 'your-openai-api-key-here'
        ]);
        break;
    default:
        echo json_encode(['error' => 'Invalid action', 'success' => false]);
        break;
}
?>