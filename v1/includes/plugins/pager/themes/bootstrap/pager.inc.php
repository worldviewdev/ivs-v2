<?php
if (!$this->needs_paging) {
    return '';
}
?>
<div class="d-flex flex-stack flex-wrap pt-10">
    <div class="fs-6 fw-semibold text-gray-700">
        Showing <?= $this->start + 1?> to <?=min($this->total_records, $this->start + $this->records_per_page)?> of <?= $this->total_records?> entries
    </div>
    <ul class="pagination">
        <?php if ($this->has_previous()) {?>
        <li class="page-item previous">
            <a href="<?=$this->previous_page?>" class="page-link">
                <i class="previous"></i>
            </a>
        </li>
        <?php } else { ?>
        <li class="page-item previous disabled">
            <span class="page-link">
                <i class="previous"></i>
            </span>
        </li>
        <?php } ?>
        
        <?php 
        $arr_numbered_links = $this->get_numbered_links();
        foreach ($arr_numbered_links as $page_num => $link) {
            if ($page_num == $this->current_page) {
        ?>
        <li class="page-item active">
            <span class="page-link"><?=$page_num?></span>
        </li>
        <?php
            } else {
        ?>
        <li class="page-item">
            <a href="<?=$link?>" class="page-link"><?=$page_num?></a>
        </li>
        <?php
            }
        }
        ?>
        
        <?php if ($this->has_next()) {?>
        <li class="page-item next">
            <a href="<?=$this->next_page?>" class="page-link">
                <i class="next"></i>
            </a>
        </li>
        <?php } else { ?>
        <li class="page-item next disabled">
            <span class="page-link">
                <i class="next"></i>
            </span>
        </li>
        <?php } ?>
    </ul>
</div>
