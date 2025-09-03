<?php
//print_r($this);
if (!$this->needs_paging) {
    return '';
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="left" valign="top" class="paging_rounded_border">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="20" align="left" valign="top" class="paging_rounded_inner_bg">
						<table width="100%" border="0" cellspacing="0" cellpadding="5">
							<tr>
								<td nowrap="nowrap" align="left" valign="middle">
									<span class="txtorange14">Showing Records:</span> <span
										class="txtblack14"><?= $this->start + 1?>
										-
										<?=min($this->total_records, $this->start + $this->records_per_page)?>
										of
										<?= $this->total_records?></span>
								</td>
								<td width="85%" align="right" valign="middle" class="paging">
									<?php if ($this->has_previous()) {?>
									<a href="<?=$this->previous_page?>"
										class="pager_link">&laquo; Previous</a>
									<?php }
									$arr_numbered_links = $this->get_numbered_links();
foreach ($arr_numbered_links as $page_num => $link) {
    if ($page_num == $this->current_page) {
        ?><span class="paging_active"><?=$page_num?></span>
									<?php
    } else {
        ?> <a href="<?=$link?>"><?=$page_num?></a>
									<?php
    }
}
if ($this->has_next()) {
    ?>
									<a href="<?=$this->next_page?>">Next
										&raquo;</a>
									<?php }?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>