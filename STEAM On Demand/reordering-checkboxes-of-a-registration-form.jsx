// rendering checkboxes at the bottom of the form"
//<script>
jQuery(document).ready(function () {
  jQuery(".mepr-payment-methods-wrapper").append(
    jQuery('input[type="checkbox"]').parent().parent()
  );
});
//</script>
//end rendering checkboxes at the bottom of the form
