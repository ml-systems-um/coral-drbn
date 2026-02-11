<?php //Temporary holding place for functions stored in the directory script; separating stuff out.
//commonly used to convert price into integer for insert into database
function cost_to_integer($price) {
    $nf = new NumberFormatter(return_number_locale(), NumberFormatter::DECIMAL);
    $parsed = $nf->parse($price, NumberFormatter::TYPE_DOUBLE);
    return $parsed * 100;
}

//commonly used to convert integer into a price for display. Add allowable negatives because sometimes we want a negative price displayed.
function integer_to_cost($price, $allowNegative = FALSE) {
    $nf = new NumberFormatter(return_number_locale(), NumberFormatter::DECIMAL);
    $nf->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, return_number_decimals());
    $nf->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, return_number_decimals());

    //Is price positive?
    $positivePrice = ($price > 0);
    $outputValue = ($positivePrice || $allowNegative) ? ($price / 100) : 0;
    return $nf->format($outputValue);
}

function normalize_date($date) {
    if (($date == "0000-00-00") || ($date == "")){
        return "";
    }else{
        return format_date($date);
    }
}

function is_null_date($date) {
    return (!$date || $date == "0000-00-00" || $date == "");
}

function previous_year($year) {
    return preg_replace_callback(
        '/(19[0-9][0-9]|2[0-9][0-9][0-9])/',
        function ($matches) { return $matches[0]-1; },
        $year,
        1
    );
}

//Watched function to catch the strings being passed into resource_sidemenu for translation
function watchString($string) {
  return $string;
}

function resource_sidemenu($links, $selected_link = '') {
  global $user;

  if (empty($selected_link)) {
    $selected_link = array_key_first($links);
  }
  
  foreach ($links as $key => $value) {
    $aria_current = '';
    $showTab = ($_GET['showTab']) ?? "";
    if ($showTab == $key) {
      $aria_current = 'aria-current="page"';
    }

    $params = array_merge( $_GET, array( 'showTab' => $key ) );
    $href = http_build_query( $params );

    if ($key != 'accounts' || $user->accountTabIndicator == '1' || $user->isAdmin) {
    ?>
    <li>
      <a href="?<?php echo $href; ?>" <?php echo $aria_current; ?> class='show<?php echo ucfirst($key); ?>'>
        <?php echo $value; ?>
        <?php if ($key == 'attachments') { ?>
          <span class='span_AttachmentNumber count'></span>
        <?php } ?>
      </a>
    </li>
    <?php
    }
  }
}

function buildSelectableHours($fieldNameBase,$defaultHour=8) {
  $html = "<select name=\"{$fieldNameBase}[hour]\"  id=\"{$fieldNameBase}_hour\">";
  for ($hour=1;$hour<13;$hour++) {
    $html .= "<option".(($hour == $defaultHour) ? ' selected':'').">{$hour}</option>";
  }
  $html .= '</select>';
  return $html;
}

function buildSelectableMinutes($fieldNameBase,$intervals=4) {
  $html = "<select name=\"{$fieldNameBase}[minute]\"  id=\"{$fieldNameBase}_minute\">";
  for ($minute=0;$minute<=($intervals-1);$minute++) {
    $html .= "<option>".sprintf("%02d",$minute*(60/$intervals))."</option>";
  }
  $html .= '</select>';
  return $html;
}

// TODO: i18n
function buildSelectableMeridian($fieldNameBase) {
  return "<select name=\"{$fieldNameBase}[meridian]\" id=\"{$fieldNameBase}_meridian\">
          <option>AM</option>
          <option>PM</option>
        </select>";
}

function buildTimeForm($fieldNameBase,$defaultHour=8,$minuteIntervals=4) {
  return buildSelectableHours($fieldNameBase,$defaultHour).buildSelectableMinutes($fieldNameBase,$minuteIntervals).buildSelectableMeridian($fieldNameBase);
}


?>