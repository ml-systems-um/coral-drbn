<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 2.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as
**   published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
**   of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

?>

<footer class="footer">
  <?php echo _("Copyright");?> &copy; <?php echo date('Y'); ?>. <?php echo _("CORAL version");?> 2024.??<br/>
  <a href="http://coral-erm.org/">
    <img src="/coral/images/coral-erm.svg" role="img" class="logo logo-dark" alt="CORAL eRM project website"/> 
    <img src="/coral/images/coral-erm-light.svg" role="img" class="logo logo-light" alt="CORAL eRM project website"/>
  </a>
  <a href="https://github.com/coral-erm/coral/issues" id="report-issue"><?php echo _("Report an Issue");?></a>
</footer>

<script>
  const CORAL_ILS_LINK=<?php echo $config->ils->ilsConnector ? 1 : 0; ?>;
  Date.format = '<?php echo return_datepicker_date_format(); ?>';
</script>

<?php
// Additional scripts and closing body/html tags in parent HTML file
?>