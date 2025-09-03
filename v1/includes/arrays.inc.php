<?php
$ARR_AGENT_COMMISSION_TYPE = array("1" => "Percent", "2" => "Flat");
$arr_cruise_meals = array("B" => "Breakfast", "L" => "Lunch", "D" => "Dinner");
$arr_meals_short = array("Breakfast" => "B", "Lunch" => "L", "Dinner" => "D", "Wine Tasting" => "WT", "Cooking Class" => "CC");
$color_array = array("#FFFFFF" => "#FFFFFF", "#113F8C" => "#113F8C", "#01A4A4" => "#01A4A4", "#00A1CB" => "#00A1CB", "#61AE24" => "#61AE24", "#D0D102" => "#D0D102", "#32742C" => "#32742C", "#D70060" => "#D70060", "#E54028" => "#E54028", "#F18D05" => "#F18D05", "#CE0027" => "#CE0027", "#EAAE4E" => "#EAAE4E", "#1F45FC" => "#1F45FC", "#6960EC" => "#6960EC", "#5CB3FF" => "#5CB3FF", "#82CAFA" => "#82CAFA", "#7FFFD4" => "#7FFFD4", "#00FFFF" => "#00FFFF", "#FFD801" => "#FFD801", "#FDD017" => "#FDD017", "#EAC117" => "#EAC117", "#C12267" => "#C12267", "#C25283" => "#C25283", "#C12283" => "#C12283", "#B93B8F" => "#B93B8F");
$arr_file_search_option = array("1" => "Agent Name", "2" => "Agency Name", "3" => "Client Name", "4" => "File Number");
$arr_tour_search_option = array("1" => "All Type", "2" => "Multi Day", "3" => "Activity", "4" => "Sight Seeing");
$ARR_SERVICE_TYPE = array("1" => "Hotel", "9" => "Unit Rental", "2" => "Transfer", "3" => "Ferry", "4" => "Train", "5" => "Flight", "6" => "Car Rental", "7" => "Tours & Activities", "8" => "Misc");
$ARR_LOCATION_TYPE=array("1"=>"Country","Region","Province","City","Town","District","Landmark","Island","Lake","Garden/Park");
//$ARR_LOCATION_TYPE = array("8" => "Island", "9" => "Lake", "10" => "Garden");
//$ARR_PAID_BY=array("1"=>"Italy Vacation Specialists","2"=>"Lead PAX","3"=>"Agency","5"=>"Supplier","4"=>"Other");
$ARR_PAID_BY = array("1" => "Italy Vacation Specialists");
$arr_file_service_status = array("0" => "Unconfirmed", "1" => "Confirmed");
$links_main_array = array("1" => "Home", "2" => "Files", "3" => "Users", "4" => "Product", "5" => "Suppliers", "6" => "Affiliates", "7" => "Resources", "8" => "Logs", "9" => "Social Media", "10" => "Reports", "11" => "Manage", "12" => "Accounting");
$arr_booking_fee_basis = array("1" => "Per Pax", "2" => "File");
$arr_service_fee_basis = array("1" => "Per Pax", "2" => "File");
$arr_file_depoist_basis = array("1" => "Per Pax", "2" => "Flat Rate", "3" => "Percentage");
$arr_payment_type = array("1" => "Deposit", "2" => "Partial Payment", "3" => "Balance", "4" => "Agent Net");
for ($i = 0; $i <= 100; $i++) {
    $arr_pax[$i] = $i;
    $arr_total_pax[$i] = $i;
}
#$arr_pax=array("0","1","2","3","4","5","6","7","8","9","10");
//$arr_total_pax=array("1"=>"1","2","3","4","5","6","7","8","9","10");
$arr_yes_no = array("No" => "No", "Yes" => "Yes");
$arr_activity_segment = array("1" => "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30");
$arr_package_segment = array("1" => "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30");
$ARR_TRANSFER_BOOKING_METHOD = array("1" => "Booking Engine", "2" => "Contract");
$ARR_HOTEL_RATING = array("3 Star" => "3 Star", "4 Star" => "4 Star", "5 Star" => "5 Star");
$arr_file_types = array("1" => "FIT", "5" => "IVS", "6" => "DII", "2" => "Group", "7" => "Visits Italy", "9" => "Wine Tours Italia", "8" => "ILT", "3" => "Special Group", "4" => "Online", "10" => "Motivation", "11" => "Information Request", "12"=>"Transfers & Tours", "13"=>"FAM Trip", "14"=>"Summit Retreats", "15"=>"Honeymoon", "16"=>"Transfers", "17"=>"Zicasso");
$arr_tour_classification = array("1" => "Multi Day", "2" => "Sight Seeing", "3" => "Activity");
//$arr_file_status=array("1"=>"Quotation","2"=>"Deposit","3"=>"Paid in Full","4"=>"Vouchers Sent","5"=>"Traveling","6"=>"Returned","7"=>"Completed","8"=>"Abandoned");
$arr_file_status = array(
    "2" => "Deposit Completed",
    //"16" => "Deposit Completed - Final Payment by Credit Card",
    "3" => "Paid in Full by Credit Card",
    "9" => "In Progress",
    "10" => "Quotation Sent - Waiting for Response",
    "11" => "To Be Assigned",
    "12" => "To Be Revised",
    "15" => "Confirmed & Waiting for Credit Card",
    "13" => "Confirmed",
    "58" => "All Services Booked",
    "8" => "Abandoned",
    "14" => "Need to Follow Up",
    "17" => "Information Request",
    "51" => "Need to follow up with a call",
    "52" => "Waiting on vendor",
    "53" => "No response from client",
    "54" => "Client canceled trip",
    "55" => "Emailed client for more information",
    //"56" => "Emailed client for more information",
    //"57" => "Response sent received email – need to follow up",
    );
$arr_file_status2 = array(
    "Deposit Completed" => "Deposit Completed",
    //"Deposit Completed - Final Payment by Credit Card" => "Deposit Completed - Final Payment by Credit Card",
    "Paid in Full by Credit Card" => "Paid in Full by Credit Card",
    "In Progress" => "In Progress",
    "Quotation Sent - Waiting for Response" => "Quotation Sent - Waiting for Response",
    "To Be Assigned" => "To Be Assigned", "To Be Revised" => "To Be Revised",
    "Confirmed & Waiting for Credit Card" => "Confirmed & Waiting for Credit Card",
    "Confirmed" => "Confirmed",
    "All Services Booked" => "All Services Booked",
    "Abandoned" => "Abandoned",
    "Need to Follow Up" => "Need to Follow Up",
    "Need to follow up with a call" => "Need to follow up with a call",
    "Waiting on vendor" => "Waiting on vendor",
    "No response from client" => "No response from client",
    "Client canceled trip" => "Client canceled trip",
    "Emailed client for more information" => "Emailed client for more information",
    //"Emailed client for more information" => "Emailed client for more information",
    //"Response sent received email – need to follow up" => "Response sent received email – need to follow up",
    );
//$arr_file_search_status=array("0"=>"Current","1"=>"Quotation","2"=>"Deposit","3"=>"Paid in Full","4"=>"Vouchers","5"=>"Traveling","6"=>"Returned","7"=>"Completed","8"=>"Abandoned","99"=>"All");
$arr_file_search_status = array(
    "99" => "All",
    "2" => "Deposit Completed",
    //"16" => "Deposit Completed - Final Payment by Credit Card",
    "3" => "Paid in Full by Credit Card",
    "9" => "In Progress",
    "10" => "Quotation Sent - Waiting for Response",
    "11" => "To Be Assigned",
    "12" => "To Be Revised",
    "15" => "Confirmed & Waiting for Credit Card",
    "13" => "Confirmed",
    "58" => "All Services Booked",
    "8" => "Abandoned",
    "14" => "Need to Follow Up",
    "51" => "Need to follow up with a call",
    "52" => "Waiting on vendor",
    "53" => "No response from client",
    "54" => "Client canceled trip",
    "55" => "Emailed client for more information",
    //"56" => "Emailed client for more information",
    //"57" => "Response sent received email – need to follow up",
    );
$arr_file_received_by = array("1" => "Phone", "2" => "Email");
$arr_file_request = array("1" => "Full Itinerary", "2" => "Services");
$arr_transfer_basis = array("1" => "Per Car", "2" => "Per Person");
$arr_suppliment_basis = array("1" => "Per Pax", "2" => "Per Vehicle");
$arr_tour_price_strategy = array("A" => "A", "B" => "B", "C" => "C");
$arr_tour_price_mode = array("Net" => "Net", "Gross" => "Gross");
$arr_cruise_price_mode = array("Net" => "Net", "Gross" => "Gross", "Fluid" => "Fluid");
$arr_tour_languages = array("English" => "English", "French" => "French", "Spanish" => "Spanish", "Other" => "Other");
$arr_hotel_extra_basis = array("1" => "Per Room", "2" => "Per Person", "3" => "Per Stay", "4" => "Per Day");
$ARR_ASSOCIATION = array("IATA" => "IATA", "CLIA" => "CLIA", "ASTA" => "ASTA", "ACTA" => "ACTA");
$ARR_AGENCY_PAYMENT_OPTION = array("Consortium" => "Consortium", "Agency" => "Direct to Agency");
//$arr_business_type=array("1"=>"Hotel","5"=>"Unit Rental","6"=>"Cruise","3"=>"DMC","4"=>"Booking Engine");
$arr_business_type = array("1" => "Hotel", "5" => "Unit Rental", "3" => "DMC", "4" => "Booking Engine", "7" => "Business", "8" => "Wineries", "9" => "Enoteca - Wine Bars", "10" => "Restaurants", "11" => "Events", "12" => "Guides", "13" => "Transportation & Drivers", "14" => "Sea-side Resorts", "15" => "Spas and Thermal Baths", "16" => "Olive Oil Estates", "17" => "Agriturismo", "18" => "Boat");
$arr_business_type_code = array("1" => "HTL", "5" => "UNR", "3" => "DMC", "4" => "BOE", "6" => "CRU", "7" => "BUS", "8" => "WIN", "9" => "ENO", "10" => "RES", "11" => "EVE", "12" => "GUI", "13" => "DAC", "14" => "SSR", "15" => "STB", "16" => "OOE", "17" => "FSA", "18" => "BOA");
$supplierTypeArr = array("7" => "Business", "8" => "Wineries", "9" => "Enoteca-Wine Bars", "10" => "Restaurants", "11" => "Events", "12" => "Guides", "13" => "Transportation & Drivers", "14" => "Sea-side Resorts", "15" => "Spas and Thermal Baths", "16" => "Olive Oil Estates", "17" => "Farm Estates/Agriturismo", "18" => "Boat");
//replace drivers-and-cars to transportation-and-drivers
$supplierTypeSlugArr = array("1" => "hotel", "5" => "unit-rental", "3" => "dmc", "4" => "booking-engine", "7" => "business", "8" => "wineries", "9" => "enoteca-wine-bars", "10" => "restaurants", "11" => "events", "12" => "guides", "13" => "transportation-and-drivers", "14" => "sea-side-resorts", "15" => "spas-and-thermal-baths", "16" => "olive-oil-estates", "17" => "agriturismo", "18" => "boat");
$arr_hotel_type = array("1" => "Air Port Hotel", "2" => "Borgo", "3" => "Convent", "4" => "Castellos", "5" => "Country Resort", "6" => "Guest House", "7" => "Hostel", "8" => "Monastery", "9" => "Pensione", "10" => "Relais", "11" => "Residence", "12" => "Seaside Resort", "13" => "Thermal Spa");
$currency_to_import = array("EUR", "USD", "CAD", "GBP", "AUD");
$ARR_CURRENCY = array(
    'EUR' => 'EUR',
    'USD' => 'USD',
    'CAD' => 'CAD',
    'GBP' => 'GBP',
    'AUD' => 'AUD'
);
$ARR_CURRENCY_SYMBOL = array(
    'EUR' => '&euro;',
    'USD' => '$',
    'CAD' => 'C$',
    'GBP' => '&pound;',
    'AUD' => 'A$'
);
$ARR_VALID_IMG_EXTS = array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp');
$ARR_WEEK_DAYS = array(
    'Mon' => 'Monday',
    'Tues' => 'Tuesday',
    'Wed' => 'Wednesday',
    'Thurs' => 'Thursday',
    'Fri' => 'Friday',
    'Sat' => 'Saturday',
    'Sun' => 'Sunday'
);
$ARR_CRUISE_WEEK_DAYS = array(
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thursday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday'
);
$ARR_WEEK_DAYS_SHORT = array(
    'Mon' => 'Mon',
    'Tues' => 'Tues',
    'Wed' => 'Wed',
    'Thurs' => 'Thurs',
    'Fri' => 'Fri',
    'Sat' => 'Sat',
    'Sun' => 'Sun'
);
$ARR_SALUTATION = array("Mr/Ms" => "Mr/Ms", "Mr" => "Mr", "Ms" => "Ms", "Mrs" => "Mrs", "Dr" => "Dr", "Rev" => "Rev", "Father" => "Father");
$arr_title = array("Mr" => "Mr", "Mrs" => "Mrs", "Ms" => "Ms");
$ARR_SALUT = array("Mr/Ms" => "&nbsp;Mr/Ms", "Mr" => "&nbsp;Mr", "Ms" => "&nbsp;Ms", "Mrs" => "&nbsp;Mrs", "Dr" => "&nbsp;Dr", "Rev" => "&nbsp;Rev");
$ARR_MONTHS = Array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec');
if ($handle = opendir(dirname(__FILE__) . '/db_arrays')) {
    while (false !== ($file = readdir($handle))) {
        if (is_file(dirname(__FILE__) . '/db_arrays/' . $file)) {
            include(dirname(__FILE__) . '/db_arrays/' . $file);
        }
    }
    closedir($handle);
}
$arr_italy_provinence = array('Agrigento (AG)' => 'Agrigento (AG)', 'Alessandria (AL)' => 'Alessandria (AL)', 'Ancona (AN)' => 'Ancona (AN)', 'Arezzo (AR)' => 'Arezzo (AR)', 'Ascoli Piceno (AP)' => 'Ascoli Piceno (AP)', 'Asti (AT)' => 'Asti (AT)', 'Avellino (AV)' => 'Avellino (AV)', 'Bari (BA)' => 'Bari (BA)', 'Belluno (BL)' => 'Belluno (BL)', 'Benevento (BN)' => 'Benevento (BN)', 'Bergamo (BG)' => 'Bergamo (BG)', 'Biella (BI)' => 'Biella (BI)', 'Bologna (BO)' => 'Bologna (BO)', 'Bolzano (BZ)' => 'Bolzano (BZ)', 'Brescia (BS)' => 'Brescia (BS)', 'Brindisi (BR)' => 'Brindisi (BR)', 'Cagliari (CA)' => 'Cagliari (CA)', 'Caltanissetta (CL)' => 'Caltanissetta (CL)', 'Campobasso (CB)' => 'Campobasso (CB)', 'Carbonia-Iglesias (CI)' => 'Carbonia-Iglesias (CI)', 'Caserta (CE)' => 'Caserta (CE)', 'Catania (CT)' => 'Catania (CT)', 'Catanzaro (CZ)' => 'Catanzaro (CZ)', 'Chieti (CH)' => 'Chieti (CH)', 'Como (CO)' => 'Como (CO)', 'Cosenza (CS)' => 'Cosenza (CS)', 'Cremona (CR)' => 'Cremona (CR)', 'Crotone (KR)' => 'Crotone (KR)', 'Cuneo (CN)' => 'Cuneo (CN)', 'Enna (EN)' => 'Enna (EN)', 'Ferrara (FE)' => 'Ferrara (FE)', 'Florence (FI)' => 'Florence (FI)', 'Foggia (FG)' => 'Foggia (FG)', 'Forli-Cesena (FC)' => 'Forli-Cesena (FC)', 'Frosinone (FR)' => 'Frosinone (FR)', 'Genoa (GE)' => 'Genoa (GE)', 'Gorizia (GO)' => 'Gorizia (GO)', 'Grosseto (GR)' => 'Grosseto (GR)', 'Imperia (IM)' => 'Imperia (IM)', 'Isernia (IS)' => 'Isernia (IS)', 'L\'Aquila (AQ)' => 'L\'Aquila (AQ)', 'La Spezia (SP)' => 'La Spezia (SP)', 'Latina (LT)' => 'Latina (LT)', 'Lecce (LE)' => 'Lecce (LE)', 'Lecco (LC)' => 'Lecco (LC)', 'Livorno (LI)' => 'Livorno (LI)', 'Lodi (LO)' => 'Lodi (LO)', 'Lucca (LU)' => 'Lucca (LU)', 'Macerata (MC)' => 'Macerata (MC)', 'Mantova (MN)' => 'Mantova (MN)', 'Massa-Carrara (MS)' => 'Massa-Carrara (MS)', 'Matera (MT)' => 'Matera (MT)', 'Media-Campidano (VS)' => 'Media-Campidano (VS)', 'Messina (ME)' => 'Messina (ME)', 'Milan (MI)' => 'Milan (MI)', 'Modena (MO)' => 'Modena (MO)', 'Naples (NA)' => 'Naples (NA)', 'Novara (NO)' => 'Novara (NO)', 'Nuoro (NU)' => 'Nuoro (NU)', 'Ogliastra (OG)' => 'Ogliastra (OG)', 'Olbia-Tempio (OT)' => 'Olbia-Tempio (OT)', 'Oristano (OR)' => 'Oristano (OR)', 'Padova (PD)' => 'Padova (PD)', 'Palermo (PA)' => 'Palermo (PA)', 'Parma (PR)' => 'Parma (PR)', 'Pavia (PV)' => 'Pavia (PV)', 'Perugia (PG)' => 'Perugia (PG)', 'Pesaro e Urbino (PU)' => 'Pesaro e Urbino (PU)', 'Pescara (PE)' => 'Pescara (PE)', 'Piacenza (PC)' => 'Piacenza (PC)', 'Pisa (PI)' => 'Pisa (PI)', 'Pistoia (PT)' => 'Pistoia (PT)', 'Pordenone (PN)' => 'Pordenone (PN)', 'Potenza (PZ)' => 'Potenza (PZ)', 'Prato (PO)' => 'Prato (PO)', 'Ragusa (RG)' => 'Ragusa (RG)', 'Ravenna (RA)' => 'Ravenna (RA)', 'Reggio Calabria (RC)' => 'Reggio Calabria (RC)', 'Reggio Emilia (RE)' => 'Reggio Emilia (RE)', 'Rieti (RI)' => 'Rieti (RI)', 'Rimini (RN)' => 'Rimini (RN)', 'Rome (RM)' => 'Rome (RM)', 'Rovigo (RO)' => 'Rovigo (RO)', 'Salerno (SA)' => 'Salerno (SA)', 'Sassari (SS)' => 'Sassari (SS)', 'Savona (SV)' => 'Savona (SV)', 'Siena (SI)' => 'Siena (SI)', 'Siracusa (SR)' => 'Siracusa (SR)', 'Sondrio (SO)' => 'Sondrio (SO)', 'Taranto (TA)' => 'Taranto (TA)', 'Teramo (TE)' => 'Teramo (TE)', 'Terni (TR)' => 'Terni (TR)', 'Trapani (TP)' => 'Trapani (TP)', 'Trento (TN)' => 'Trento (TN)', 'Treviso (TV)' => 'Treviso (TV)', 'Trieste (TS)' => 'Trieste (TS)', 'Turin (TO)' => 'Turin (TO)', 'Udine (UD)' => 'Udine (UD)', 'Valle d\'Aosta (AO)' => 'Valle d\'Aosta (AO)', 'Varese (VA)' => 'Varese (VA)', 'Venice (VE)' => 'Venice (VE)', 'Verbano-Cusio-Ossola (VB)' => 'Verbano-Cusio-Ossola (VB)', 'Vercelli (VC)' => 'Vercelli (VC)', 'Verona (VR)' => 'Verona (VR)', 'Vibo Valentia (VV)' => 'Vibo Valentia (VV)', 'Vicenza (VI)' => 'Vicenza (VI)', 'Viterbo (VT)' => 'Viterbo (VT)');
$arr_italy_sub_region = array('Aeolian Islands' => 'Aeolian Islands', 'Amalfi Coast' => 'Amalfi Coast', 'B&amp;B Circuit' => 'B&amp;B Circuit', 'Cedri Riviera' => 'Cedri Riviera', 'Cinque Terre' => 'Cinque Terre', 'Costa Smeralda' => 'Costa Smeralda', 'Elba Island' => 'Elba Island', 'Isle of Capri' => 'Isle of Capri', 'Isola Ischia' => 'Isola Ischia', 'Italian Riviera' => 'Italian Riviera', 'Lake District' => 'Lake District', 'Lake District - Como' => 'Lake District - Como', 'Lake District - Garda' => 'Lake District - Garda', 'Lake District - Iseo' => 'Lake District - Iseo', 'Lake District - Maggiore' => 'Lake District - Maggiore', 'Lake District - Orta' => 'Lake District - Orta', 'The Chianti' => 'The Chianti', 'The Cilento' => 'The Cilento', 'The Dolomites' => 'The Dolomites', 'The Langhe' => 'The Langhe', 'The Maremma' => 'The Maremma', 'The Mugello' => 'The Mugello', 'The Sagranti' => 'The Sagranti', 'Tuscan Coast' => 'Tuscan Coast', 'Val D\'Orcia' => 'Val D\'Orcia', 'Val di Pesa' => 'Val di Pesa');
function remove_accent($str) {
    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    return str_replace($a, $b, $str);
}
function post_slug($str) {
    return preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($str));
}
$ARR_ACCOMMDATION_VOUCHER = array("B&B" => "Breakfast", "D" => "Dinner", "FB" => "Breakfast, Lunch, Dinner", "HB" => "Breakfast, Dinner", "L" => "Lunch", "None" => "None", "Self Catering" => "Self Catering");
$tour_category_arr = array(
    "26" => "Private Italy Shore Trips",
    "27" => "Private & Small Group Rome Tours",
    "28" => "Private & Small Group Tours at Vatican City",
    "29" => "Private & Small Group Florence Tours",
    "30" => "Private & Small Group Tuscany - Umbria & the Cinque Terre Tours",
    "31" => "Private & Small Group Venice Tours",
    "32" => "Southern Italy Private & Small Group Tours",
    "33" => "Lake District Tours",
    "34" => "Sicily Tours and Culinary Experiences",
    "35" => "Italy's Magnificent Island Tours",
//"36"=>"Events and Adventures for Singles",
    "24" => "Privately Guided Tours in Italy",
//"25"=>"Guaranteed Departure Tours",
    "25" => "Small Group Vacations in Italy",
    "23" => "Shore Excursions and Pre & Post Cruise Mini-Stays",
//"2" => "Exclusive Privately Guided Tours in Italy",
    "3" => "Small Group Sightseeing Tours in Italy",
    "5" => "Max 8 Participant Regional Tours",
    "6" => "Cooking Classes & Gourmet Food & Wine Tours",
    "7" => "Cycling and Hiking Tours in Italy",
    "9" => "Family Vacations",
    "10" => "Select Experiences",
//"12"=>"Experience Art and Craft Making in Italy",
    "13" => "Garden Tours",
    "14" => "Nature Tours including Wildlife and Bird watching Tours in Italy",
    "15" => "Walking Tours in Italy's Historic Cities",
    "17" => "Horseback Riding in Italy",
    "18" => "Art & Artistans in Italy",
    "19" => "Featured Tours",
    "20" => "Regional Fam Trips for Travel Writers and Travel Agents",
    "21" => "Romantic Holidays and Honeymoon Packages",
    "22" => "Prestige Tours",
    "36" => "Exclusive & Independent Tours of Italy",
    "37" => "Guaranteed Departure Tours",
    "38" => "Health & Wellness Retreats",
	"39" => "Tours for Women by Women",
);
$tour_cat_slug_arr = array(
    "26" => "Private-Italy-Shore-Trips",
    "27" => "Private-Small-Group-Rome-Tours",
    "28" => "Small-Group-Tours-Vatican-City",//"Private-Small-Group-Tours-at-Vatican-City",
    "29" => "Small-Group-Florence", //"Private-Small-Group-Florence-Tours",
    "30" => "Small-Group-Tuscany-Umbria-Cinque-Terre-Tours",//"Private-Small-Group-Tuscany-Umbria-the-Cinque-Terre-Tours",
    "31" => "Private-Small-Group-Venice-Tours",
    "32" => "Southern-Italy-Small-Group-Tours",//"Southern-Italy-Private-Small-Group-Tours",
    "33" => "lake-district-tours",
    "34" => "sicily-tours-culinary-experiences",//"sicily-tours-and-culinary-experiences",
    "35" => "italy-magnificent-island-tours",
//"36"=>"events-and-adventures-for-singles",
    "24" => "Privately-Guided-Tours",
//"25"=>"Guaranteed-Departure-Tours",
    "25" => "small-group-vacations-in-italy",
    "23" => "shore-excursions-italy",
//"2" => "italy-private-tours",
    "3" => "small-group-tours-italy",
    "5" => "italy-eight-pax-max-vacations",
    "6" => "cooking-class-gourmet-food-and-wine",
    "7" => "walking-cycling-tours-italy",
    "9" => "italy-family-vacations",
    "10" => "unique-experiences-in-italy",
    "12" => "art-tours-italy",
    "13" => "italy-garden-tours",
    "14" => "nature-eco-tours-italy",
    "15" => "city-walking-tours-italy",
    "17" => "Horseback-Riding-in-Italy",
    "18" => "Art-Artistans-in-Italy",
    "19" => "Featured-Tours",
    "20" => "Regional-Fam-Travel-Writers-Agents",//"Regional-Fam-Trips-for-Travel-Writers-and-Travel-Agents",
    "21" => "italy-weddings-honeymoons",//"italy-weddings-honeymoons-romance",
    "22" => "prestige-tours-italy",
    "36" => "independent-tours-of-italy",
    "37" => "guaranteed-departure-tours",
    "38" => "health-and-wellness-retreats",
    "39" => "tours-for-women-by-women",
);
$tour_disp_category = array(
    "3" => "Small Group Sightseeing",
    "24" => "Private Tours",
    "23" => "Shore Excursions",
//"2" => "Exclusive Privately Guided Tours in Italy",
//"5" => "Regional Tours"
);
$categorySlug301 = array(
    'guaranteed-departure-tours'=>'guaranteed-deaprture-tours',
    'Small-Group-Florence'=>'Private-Small-Group-Florence-Tours',
    'Small-Group-Tours-Vatican-City'=>'Private-Small-Group-Tours-at-Vatican-City',
    'Small-Group-Tuscany-Umbria-Cinque-Terre-Tours'=>'Private-Small-Group-Tuscany-Umbria-the-Cinque-Terre-Tours',
    'Regional-Fam-Travel-Writers-Agents'=>'Regional-Fam-Trips-for-Travel-Writers-and-Travel-Agents',
    'italy-weddings-honeymoons'=>'italy-weddings-honeymoons-romance',
    'sicily-tours-culinary-experiences'=>'sicily-tours-and-culinary-experiences',
    'Southern-Italy-Small-Group-Tours'=>'Southern-Italy-Private-Small-Group-Tours'
);
$search_arr = array("2" => "Tours", "1" => "Location", "3" => "Accommodation");
$occupancy_arr = array("77" => "Single", "78" => "Double", "79" => "Twin", "80" => "Triple",);
$package_arr = array("1" => "Classic", "2" => "Deluxe");
$loc_cat_arr = array("1" => "Gardens in Italy", "7" => "Islands in Italy");
//$loc_cat_arr=array("1"=>"Gardens in Italy","4"=>"Lake District Italy","7"=>"Islands in Italy","9"=>"Calabria and the Deep South"); //"8"=>"Sicily","5"=>"Tuscany & Cinque Terre Natonal Park","2"=>"Art Cities","6"=>"Amalfi Coast, Pompeii & Naples","3"=>"Thermal Hot Springs & Spas",
$arr_pax_list = array("0" => "&nbsp;0", "&nbsp1", "&nbsp2", "&nbsp;3", "&nbsp;4", "&nbsp;5", "&nbsp;6", "&nbsp;7", "&nbsp;8", "&nbsp;9", "&nbsp;10");
$not_banner_page_arr = array("tour_cart.php", "shopping-cart.php", "checkout.php", "thanks.php");
$arr_tour_type = array("2" => "Sightseeing Tours", "1" => "Regional Tours & Vacation Packages", "3" => "Cooking Classes");
$arr_search_loc = array("Rome" => "Rome", "Vatican" => "Vatican", "Venice" => "Venice", "Florence" => "Florence", "Milan" => "Milan", "Tuscany" => "Tuscany", "Amalfi-Coast" => "Amalfi Coast", "Sicily" => "Sicily");
$airport_arr = array("Alghero" => "Alghero", "Ancona" => "Ancona", "Bari" => "Bari", "Bergamo" => "Bergamo", "Bologna" => "Bologna", "Brindisi" => "Brindisi", "Cagliari" => "Cagliari", "Florence" => "Florence", "Genoa" => "Genoa", "Milan" => "Milan", "Olbia" => "Olbia", "Perugia" => "Perugia", "Pescara" => "Pescara", "Pisa" => "Pisa", "Rome" => "Rome", "Siena" => "Siena", "Turin" => "Turin", "Venice" => "Venice");
$status_arr = array("Active" => "Active", "Inactive" => "Inactive", "Delete" => "Delete");
$sortTypeArr = array("1" => "Display by Departure date", "3" => "Display by Arrival date", "2" => "Display by Recently added", "4" => "Upcoming trips first (Chronological)", "5" => "Agent Commission (Paid)", "6" => "Agent Commission (Unpaid)");
$action_type_arr = array("1" => "Collecting Final Payments", "2" => "Following Up with Clients", "3" => "Hotel Option Expires");
$arr_tourPax = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8');
$paymentStatusArr = array("No" => "Not Paid", "Yes" => "Paid");
$paymentByArr = array("1" => "By CC", "2" => "By Wire");
$ARR_PURPOSE = array("Information request" => "Information request", "Enquiries regarding an existing reservation" => "Enquiries regarding an existing reservation", "Suggestions" => "Suggestions", "General enquiries" => "General enquiries", "Other" => "Other");
$locations_url_array = array();
$sql = db_query("SELECT tag_id, tag_name, location_slug FROM mv_tags WHERE tag_main_parent='430' AND tag_status='Active' and location_slug!='' AND is_show_in_dest='Yes' and tag_id!='430'  and location_slug!='christmas_in_italy'");
if (mysqli_num_rows($sql) > 0) {
    $count = 0;
    while ($res = mysqli_fetch_assoc($sql)) { // gunakan fetch_assoc agar array associatif
        $tag_id = $res['tag_id'];
        $key = $res['tag_name'];
        $loc_url = implode("/", location_slug_list($tag_id));
        $value = "<a href='" . SITE_WS_PATH . "/" . $loc_url . ".htm' class='new_window' style='text-decoration:underline;font-weight:bold;'>" . $key . "</a>";
        $locations_url_array[$key] = $value;
        // asumsikan db_query() menghasilkan mysqli_query() dengan koneksi aktif
        $sql2 = db_query("SELECT tag_id, tag_name, location_slug FROM mv_tags WHERE tag_parent_id='$tag_id' AND is_show_in_dest='Yes' AND tag_status!='Delete' ORDER BY tag_name");
        if (mysqli_num_rows($sql2) > 0) {
            while ($res2 = mysqli_fetch_assoc($sql2)) {
                $tag_id2 = $res2['tag_id'];
                $key2 = $res2['tag_name'];
                $loc_url2 = implode("/", location_slug_list($tag_id2));
                $value2 = "<a href='" . SITE_WS_PATH . "/" . $loc_url2 . ".htm' class='new_window' style='text-decoration:underline;font-weight:bold;'>" . $key2 . "</a>";
                $locations_url_array[$key2] = $value2;
            }
        }
    }
}
$loc_sql = db_query("select * from mv_tags where tag_parent_id='2' and tag_status!='Delete' AND is_show_in_dest='Yes' and tag_id!='430' order by tag_name");
while ($res2 = mysqli_fetch_array($loc_sql, MYSQLI_ASSOC)) {
    $sql = db_query("SELECT tag_id, tag_name, location_slug FROM mv_tags WHERE tag_parent_id='" . $res2['tag_id'] . "' AND is_show_in_dest='Yes' AND tag_status!='Delete' ORDER BY tag_name");
    if (mysqli_num_rows($sql) > 0) {
        $count = 0;
        while ($res = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
            $tag_id = $res['tag_id'];
            $key = $res['tag_name'];
            $loc_url = implode("/", location_slug_list($tag_id));
            $value = "<a href='" . SITE_WS_PATH . "/" . $loc_url . ".htm' class='new_window' style='text-decoration:underline;font-weight:bold;'>" . $key . "</a>";
            $locations_url_array[$key] = $value;
        }
    }
}
$villByRegionArr = array(
    "southern_italy" => array(
        "amalfi-coast" => "Amalfi Coast",
        "cilento-coast" => "Cilento Coast",
        "apulia-and-basilicata" => "Apulia and Basilicata",
        "sicily" => "Sicily",
        "sardinia" => "Sardinia",
    ),
    "central_italy" => array(
        "tuscany" => "Tuscany",
        "umbria" => "Umbria",
        "rome-and-lazio" => "Rome and Lazio",
        "adriatic-coast-and-the-marches" => "Adriatic Coast and The Marches",
    ),
    "northern_italy" => array(
        "liguria-and-cinque-terre" => "Liguria and Cinque terre",
        "veneto-and-venice" => "Veneto and Venice",
        "piedmont-and-lake-maggiore" => "Piedmont and Lake Maggiore",
        "lake-garda" => "Lake Garda",
        "lombardy-and-lake-como" => "Lombardy and Lake Como",
    )
);
$villByRegionSlugArr = array(
    "amalfi-coast" => "Amalfi Coast",
    "cilento-coast" => "Cilento Coast",
    "apulia-and-basilicata" => "Apulia and Basilicata",
    "sicily" => "Sicily",
    "sardinia" => "Sardinia",
    "tuscany" => "Tuscany",
    "umbria" => "Umbria",
    "rome-and-lazio" => "Rome and Lazio",
    "adriatic-coast-and-the-marches" => "Adriatic Coast and The Marches",
    "liguria-and-cinque-terre" => "Liguria and Cinque terre",
    "veneto-and-venice" => "Veneto and Venice",
    "piedmont-and-lake-maggiore" => "Piedmont and Lake Maggiore",
    "lake-garda" => "Lake Garda",
    "lombardy-and-lake-como" => "Lombardy and Lake Como",
);
$villByRegionSlugIdArr = array(
    "amalfi-coast" => 1,
    "cilento-coast" => 2,
    "apulia-and-basilicata" => 3,
    "sicily" => 4,
    "sardinia" => 5,
    "tuscany" => 6,
    "umbria" => 7,
    "rome-and-lazio" => 8,
    "adriatic-coast-and-the-marches" => 9,
    "liguria-and-cinque-terre" => 10,
    "veneto-and-venice" => 11,
    "piedmont-and-lake-maggiore" => 12,
    "lake-garda" => 13,
    "lombardy-and-lake-como" => 14
);
$arrPageNotSlider = array('thank-you.php','tour-request.php','client-survey.php','invoice-payment.php', 'accept_payment.php', 'proceed-payment.php', 'cc_auth_form.php', 'cart.php', 'thanks.php', 'transfers.php', 'book_transfer.php', 'locations.php', 'login.php', 'contact-us.php', 'fam_trip_detail.php', 'tour_detail_new.php', 'cc_online_payment.php', '404.php', 'invoice-payment-test.php', 'customizable-tour.php', 'escorted-tour.php', 'private-tour.php', 'customize-tour.php');
$arrCSQ = array(
    "csqn"=>"What is your name",
    "csqe"=>"What is your email address",
    "csqp"=>"What is your phone number",
    "csq1"=>"How long have you or your company been in business and are you interested in increasing your sales or business",
    "csq2"=>"How many trips a year do you book for your clients to Italy",
    "csq3"=>"Why are you interested in participating in this particular trip and what are you hoping to learn",
    "csq4"=>"Where are you located",
    "csq5"=>"Do you plan on bringing a companion",
    "csq6"=>"What other areas in Italy or the Mediterranean are you interested in learning about",
);
$noOfNights = array(); $k=7; $w=3;
for($i=1; $i<=19; $i++){
    if($i<15){
        $noOfNights[$k.' nights'] = $k.' nights';
        $k++;
    }else if($i>=15 && $i<19){
        $noOfNights[$w.' weeks'] = $w.' weeks';
        $w++;
    }else{
        $noOfNights['over 6 weeks'] = 'over 6 weeks';
    }
}
$noOfAdutls = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '10+' => '10+');
$perPersonBudget = array();
$first = 1000; $second = 2000;
for($i=1; $i<=9; $i++){
    $option = 'Per Person: €'.number_format($first, 0, '', ',').' - €'.number_format($second, 0, '', ',').' Euro';
    $key = $first.'-'.$second;
    $perPersonBudget[$option] = $option;
    $tmp = $first;
    $first = $second;
    if($i < 9)
//        $second = $second+500;
//    else if($i >= 5 && $i < 12)
        $second = $second+1000;
//    else if($i >= 12 && $i < 17)
//        $second = $second+2000;
    else {
        $option = 'Per Person: Over €'.number_format($second, 0, '', ',').' Euro';
        $perPersonBudget[$option] = $option;
    }
}
$isBudgetFlexible = array(
    '1'=>'The above is my maximum budget',
    '2'=>'Flexible: I can increase up to 20% if needed',
    '3'=>"Very flexible: Plan me the trip I want. Don't focus on specific budget"
);
$stageInPlaning = array(
    '1'=>"Still dreaming . . . not sure I'm going to take this trip",
    //'2'=>"I know I'm going somewhere, just not sure which country",
    '3'=>"I'm definitely going...let's go!"
);
$agrGroupAdult = array('18-30'=>'18-30', '31-50'=>'31-50', '51-64'=>'51-64', '65+'=>'65+');
$agrGroupChild = array('0-2'=>'0-2', '3-7'=>'3-7', '8-12'=>'8-12', '13-17'=>'13-17');
$typeOfTravel = array(
    '1'=>"Custom Trip Package:<br> <span>Be on your own schedule. Activities or day tours can be private or shared.</span> ",
    '2'=>"Scheduled Group Tour:<br> <span>Join a multi-day, guided group tour with fixed departure dates.</span> ",
    '3'=>"I would like my Travel Specialists to make suggestions based on my interests."
);
$levelOfAccom = array('5 Stars'=>'5 Stars', '4 Stars'=>'4 Stars', '3 Stars'=>'3 Stars', 'Private Villa (from Saturday to Saturday)'=>'Private Villa (from Saturday to Saturday)', 'Agriturismo/Wine Resort'=>'Agriturismo/Wine Resort');
$otherServicesNeeded = array(
    '1'=>' Activities, Tour Guides, & Unique experiences',
    '2'=>' Transportation',
);
$supPaymentType = array("Deposit" => "Deposit", "Balance" => "Balance", "Full" => "Full");
$userIdArr = array(1, 43, 83);
// File status array
$arr_file_status = array(
    "" => "Select Status",
    "2" => "Deposit Completed",
    "3" => "Paid in Full by Credit Card",
    "9" => "In Progress",
    "10" => "Quotation Sent - Waiting for Response",
    "11" => "To Be Assigned",
    "12" => "To Be Revised",
    "15" => "Confirmed & Waiting for Credit Card",
    "13" => "Confirmed",
    "58" => "All Services Booked",
    "8" => "Abandoned",
    "14" => "Need to Follow Up",
    "17" => "Information Request",
    "51" => "Need to follow up with a call",
    "52" => "Waiting on vendor",
    "53" => "No response from client",
    "54" => "Client canceled trip",
    "55" => "Emailed client for more information"
);
?>