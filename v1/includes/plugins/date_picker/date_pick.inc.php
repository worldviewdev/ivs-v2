<input
  class="txtbox"
  type="text"
  name="<?=$jscal_input_name?>"
  id="<?=$jscal_input_name?>"
  size="10"
  readonly
  value="<?=$jscal_def_date?>"
  <?php if (!empty($validation)) { ?>
    alt="<?=$validation?>" emsg="<?=$validation_msg?>"
  <?php } ?>
/>
<img
  src="<?=DATE_PICKER_WS_PATH?>/jscal/img.gif"
  id="<?=$jscal_input_name?>_trigger"
  style="cursor:pointer; border:1px solid #e5e7eb;"
  align="absmiddle"
  title="Date selector"
/>
<script>
// Inisialisasi Flatpickr (single date, material look)
(function(){
  var el = document.getElementById("<?=$jscal_input_name?>");
  var fp = flatpickr(el, {
    dateFormat: "Y-m-d",       // format ke server (sama seperti %Y-%m-%d)
    altInput: true,            // tampilan human readable
    altFormat: "D, d M Y",
    defaultDate: "<?=$jscal_def_date?>",
    allowInput: false,         // tetap readonly
    // minDate: "today",       // aktifkan jika mau blok tanggal lampau
    // disableMobile: true,    // paksa UI desktop di mobile (opsional)
    // IMPORTANT: Don't use toISOString() for handling dates as it causes timezone shifts
    // Instead, always use formatDate() from the Flatpickr instance for date formatting
    onChange: function(selectedDates, dateStr, instance) {
      if (selectedDates.length === 1) {
        // When using direct dateStr, ensure dateFormat above is set to "Y-m-d"
        // This prevents timezone issues when handling dates
        var formattedDate = dateStr;
        // If you need to update another field:
        // document.getElementById("some_hidden_id").value = formattedDate;
      }
    }
  });
  // Jadikan <img> sebagai tombol pembuka calendar
  document.getElementById("<?=$jscal_input_name?>_trigger")
    .addEventListener("click", function(){ fp.open(); });
})();
</script>
<style>
/* Sentuhan modern: rounded + soft shadow */
.flatpickr-calendar {
  border-radius: 12px;
  box-shadow: 0 16px 40px rgba(0,0,0,.15);
}
</style>