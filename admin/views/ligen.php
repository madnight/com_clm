<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewLigen
{
	public static function setLigaToolbar($new)
	{
		if (!$new) { $text = JText::_( 'Edit' );}
		else { $text = JText::_( 'New' );}
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_( 'LEAGUE_BUTTON_7' ).': [ '. $text.' ]', 'clm_headmenu_liga.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::cancel();
	}

	public static function liga(&$row, $lists, $option, $new )
	{
	CLMViewLigen::setLigaToolbar($new);
	JRequest::setVar( 'hidemainmenu', 1 );

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$rang	= $config->rangliste;
	$sl_mail= $config->sl_mail;
	$countryversion= $config->countryversion;
	?>
	<?php 
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $row->params);
	$row->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$key = substr($value,0,$ipos);
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$row->params[$key] = substr($value,$ipos+1);
		}
	}	
	if (!isset($row->params['btiebr1']) OR $row->params['btiebr1'] == 0) {   //Standardbelegung
		$row->params['btiebr1'] = 1;
		$row->params['btiebr2'] = 2;
		$row->params['btiebr3'] = 3;
		$row->params['btiebr4'] = 4;
		$row->params['btiebr5'] = 0;
		$row->params['btiebr6'] = 0; }
	if (!isset($row->params['bnhtml']) OR $row->params['bnhtml'] == 0) {   //Standardbelegung
		$row->params['bnhtml'] = 5; }
	if (!isset($row->params['bnpdf']) OR $row->params['bnpdf'] == 0) {   //Standardbelegung
		$row->params['bnpdf'] = 4; }
	if (!isset($row->params['pgntype']))  {   //Standardbelegung
		$row->params['pgntype'] = 0; }
	if (!isset($row->params['pgnlname']))  {   //Standardbelegung
		$row->params['pgnlname'] = ''; }
	if (!isset($row->params['anz_sgp']))  {   //Standardbelegung
		$row->params['anz_sgp'] = 1; }
	if (!isset($row->params['deadline_roster']))  {   //Standardbelegung
		$row->params['deadline_roster'] = '0000-00-00'; }
	if (!isset($row->params['color_order']))  {   //Standardbelegung
		$row->params['color_order'] = '1'; }
	if (!isset($row->params['incl_to_season']))  {   //Standardbelegung
		if ($row->liga_mt == 0) 
			$row->params['incl_to_season'] = '1'; 
		else 
			$row->params['incl_to_season'] = '0'; }
	?>
	
	<script language="javascript" type="text/javascript">

		 Joomla.submitbutton = function (pressbutton) { 		
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_1', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_2', true ); ?>" );
			} else if (form.stamm.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_3', true ); ?>" );
			} else if (form.ersatz.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_4', true ); ?>" );
			} else if (form.teil.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_5', true ); ?>" );
			} else if (form.runden.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_6', true ); ?>" );
			} else if ( getSelectedValue('adminForm','durchgang') == "" ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_7', true ); ?>" );
			} else if (form.anz_sgp.value < 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
			} else if (form.anz_sgp.value > 20 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_8', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
 
		</script>

 <form action="index.php" method="post" name="adminForm" id="adminForm">
  <div class="width-60 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_DATA' ); ?></legend>
      <table class="paramlist admintable">

	<tr>
	<td width="20%" nowrap="nowrap">
	<label for="name"><?php echo JText::_( 'LEAGUE_NAME' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="name" id="name" size="20" maxlength="30" value="<?php echo $row->name; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="sl"><?php echo JText::_( 'LEAGUE_CHIEF' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['sl']; ?>
	</td>
	</tr>
	<?php
	// Kategorien
	list($parentArray, $parentKeys) = CLMCategoryTree::getTree();
	if (count($parentArray) > 0)  { // nur, wenn Kategorien existieren
		$parentlist[]	= JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($parentArray as $key => $value) {
			$parentlist[]	= JHtml::_('select.option',  $key, $value, 'id', 'name' );
		}
		$catidAlltime = JHtml::_('select.genericlist', $parentlist, 'catidAlltime', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($row->catidAlltime));
		$catidEdition = JHtml::_('select.genericlist', $parentlist, 'catidEdition', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($row->catidEdition));
	}
	if (isset($catidAlltime)) { 
	?>
		<tr>
			<td colspan="1" class="paramlist_key">
				<label for="category">
					<?php echo JText::_( 'CATEGORY_ALLTIME' ); ?>:
				</label>
			</td>
			<td colspan="2" class="paramlist_value">
				<?php echo $catidAlltime; ?>
			</td>
			<td colspan="1" class="paramlist_key">
				<label for="category">
					<?php echo JText::_( 'CATEGORY_EDITION' ); ?>:
				</label>
			</td>
			<td colspan="2" class="paramlist_value">
				<?php echo $catidEdition; ?>
			</td>
		</tr>
		<tr>
			<td colspan="1" class="paramlist_key">
							<?php echo JText::_('OPTION_ADDCATTONAME'); ?>:
			</td>
			<td colspan="5" class="paramlist_value">
				<?php
				$options = array();
				$options[0] = JText::_('OPTION_ADDCATTONAME_0');
				$options[1] = JText::_('OPTION_ADDCATTONAME_1');
				$options[2] = JText::_('OPTION_ADDCATTONAME_2');
				$optionlist = array();
				foreach ($options as $key => $val) {
					$optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name' );
				}
				echo JHtml::_('select.genericlist', $optionlist, 'params[addCatToName]', 'class="inputbox"', 'id', 'name', (isset($row->params['addCatToName']) ? $row->params['addCatToName'] : "0"));
				?>
			</td>
		</tr>
	<?php
	}
	?>
	<tr>
	<td nowrap="nowrap">
	<label for="saison"><?php echo JText::_( 'LEAGUE_SEASON' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['saison']; ?>
	</td>

	<td nowrap="nowrap">
	<label for="rang"><?php echo JText::_( 'LEAGUE_LIST_TYPE' ); ?></label>
	</td><td colspan="2">
	<?php if ($rang == 0) { ?>
	<?php echo $lists['gruppe']; ?>
	</td>
	</tr>
	<?php } if ($rang == 1) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_RANK' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="1" />
	<?php }
	if ($rang == 2) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_LIST' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="0" />
	<?php } ?>

	<tr>
	<td nowrap="nowrap">
	<label for="teil"><?php echo JText::_( 'LEAGUE_TEAMS' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="teil" id="teil" size="4" maxlength="4" value="<?php echo $row->teil; ?>" />
	</td>
        <td width="40" class="key" title="<?php echo JText::_( 'LEAGUE_DEADLINE_ROSTER_HINT' ); ?>">
           	<label for="params['deadline_roster']">
               	<?php echo JText::_( 'LEAGUE_DEADLINE_ROSTER' ); ?> 
           	</label>
        </td>
        <td>
           	<?php echo JHtml::_('calendar', $row->params['deadline_roster'], "params['deadline_roster']", "params['deadline_roster']", '%Y-%m-%d', array('class'=>'text_area', 'size'=>'19',  'maxlength'=>'19')); ?>
        </td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="stammspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_1' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="stamm" id="stamm" size="4" maxlength="4" value="<?php echo $row->stamm; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="erstatzspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_2' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="ersatz" id="ersatz" size="4" maxlength="4" value="<?php echo $row->ersatz; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden"><?php echo JText::_( 'LEAGUE_ROUNDS' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="runden" id="runden" size="4" maxlength="4" value="<?php echo $row->runden; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="durchgang"><?php echo JText::_( 'LEAGUE_DG' ); ?></label>
	</td><td colspan="2">
		<select name="durchgang" id="durchgang" value="<?php echo $row->durchgang; ?>" size="1">
			<option <?php if ($row->durchgang < 2) {echo 'selected="selected"';} ?>>1</option>
			<option <?php if ($row->durchgang == 2) {echo 'selected="selected"';} ?>>2</option>
			<option <?php if ($row->durchgang == 3) {echo 'selected="selected"';} ?>>3</option>
			<option <?php if ($row->durchgang == 4) {echo 'selected="selected"';} ?>>4</option>
		</select>
	</td>
</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="params['color_order']"><?php echo JText::_( 'LEAGUE_COLOR_ORDER' ); ?></label>
	</td><td colspan="2">
		<select name="params['color_order']" id="params['color_order']" value="<?php echo $row->params['color_order']; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="1" <?php if ($row->params['color_order'] == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_1' );?></option>
		<option value="2" <?php if ($row->params['color_order'] == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_2' );?></option>
		<option value="3" <?php if ($row->params['color_order'] == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_3' );?></option>
		<option value="4" <?php if ($row->params['color_order'] == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_4' );?></option>
		<option value="5" <?php if ($row->params['color_order'] == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_5' );?></option>
		<option value="6" <?php if ($row->params['color_order'] == 6) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_COLOR_ORDER_6' );?></option>
		</select>
	</td>
	<td nowrap="nowrap"></td><td colspan="2"></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden_modus"><?php echo JText::_( 'LEAGUE_PAIRING_MODE' ); ?></label>
	</td><td colspan="2">
		<select name="runden_modus" id="runden_modus" value="<?php echo $row->runden_modus; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="1" <?php if ($row->runden_modus == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_2' );?></option>
		<option value="2" <?php if ($row->runden_modus == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_3' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="heim"><?php echo JText::_( 'LEAGUE_HOME' ); ?></label>
	</td><td colspan="2"><fieldset class="radio">
		<?php echo $lists['heim']; ?>
	</fieldset></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="ersatz_regel"><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL' ); ?></label>
	</td><td colspan="2">
		<select name="ersatz_regel" id="ersatz_regel" value="<?php echo $row->ersatz_regel; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->ersatz_regel == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_0' );?></option>
		<option value="1" <?php if ($row->ersatz_regel == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_1' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="anz_sgp"><?php echo JText::_( 'LEAGUE_ANZ_SGP' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="anz_sgp" id="anz_sgp" size="4" maxlength="4" value="<?php echo $row->params['anz_sgp'] ?>" />
	</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<?php echo JText::_('OPTION_AUTODWZ'); ?>:
		</td>
		<td colspan="2" class="paramlist_value">
			<?php 
			$options = array();
			if ($countryversion == "de") {
				$options[0] = JText::_('OPTION_AUTODWZ_0');
				$options[1] = JText::_('OPTION_AUTODWZ_1');
				$options[2] = JText::_('OPTION_AUTODWZ_2');
				$options[3] = JText::_('OPTION_AUTODWZ_3');
			} else {
				$options[2] = JText::_('OPTION_AUTODWZ_9');
			}
			$optionlist = array();
			foreach ($options as $key => $val) {
				$optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name' );
			}

			if ($countryversion == "de") 
				echo JHtml::_('select.genericlist', $optionlist, 'params[autoDWZ]', 'class="inputbox"', 'id', 'name', (isset($row->params['autoDWZ']) ? $row->params['autoDWZ'] : "0")); 
			else
				echo JHtml::_('select.genericlist', $optionlist, 'params[autoDWZ]', 'class="inputbox"', 'id', 'name', "2"); 
			?>

		</td>
		<td class="paramlist_key">
			<?php echo JText::_('OPTION_AUTORANKING'); ?>:
		</td>
		<td colspan="2" class="paramlist_value">
			<?php 
			$options = array();
			$options[0] = JText::_('OPTION_AUTORANKING_0');
			$options[1] = JText::_('OPTION_AUTORANKING_1');
			$optionlist = array();
			foreach ($options as $key => $val) {
				$optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name' );
			}
			echo JHtml::_('select.genericlist', $optionlist, 'params[autoRANKING]', 'class="inputbox"', 'id', 'name', (isset($row->params['autoRANKING']) ? $row->params['autoRANKING'] : "0")); ?>
		</td>
	</tr>
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_VALUATION' ); ?></legend>
      <table class="paramlist admintable">

	<tr>
	<td nowrap="nowrap">&nbsp;</td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_1' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_2' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_3' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_4' );?></td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="punkte_modus"><?php echo JText::_( 'LEAGUE_MATCH_VALUATION' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="sieg" id="sieg" size="4" maxlength="4" value="<?php if($row->sieg !=""){ echo $row->sieg;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="remis" id="remis" size="4" maxlength="4" value="<?php if($row->remis !=""){ echo $row->remis;} else { echo "0.5";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="nieder" id="nieder" size="4" maxlength="4" value="<?php if($row->nieder !=""){ echo $row->nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="antritt" id="antritt" size="4" maxlength="4" value="<?php if($row->antritt !=""){ echo $row->antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="man_punkte"><?php echo JText::_( 'LEAGUE_TEAM_POINTS' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_sieg" id="man_sieg" size="4" maxlength="4" value="<?php if($row->man_sieg !=""){ echo $row->man_sieg;} else { echo "2";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_remis" id="man_remis" size="4" maxlength="4" value="<?php if($row->man_remis !=""){ echo $row->man_remis;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_nieder" id="man_nieder" size="4" maxlength="4" value="<?php if($row->man_nieder !=""){ echo $row->man_nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_antritt" id="man_antritt" size="4" maxlength="4" value="<?php if($row->man_antritt !=""){ echo $row->man_antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="sieg_bed"><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="sieg_bed" id="sieg_bed" value="<?php echo $row->sieg_bed; ?>" size="1">
		<option value="1" <?php if ($row->sieg_bed == 1) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_1' );?></option>
		<option value="2" <?php if ($row->sieg_bed == 2) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_2' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="b_wertung"><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS' ); //klkl?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="b_wertung" id="b_wertung" value="<?php echo $row->b_wertung; ?>" size="1">
		<option value="0" <?php if ($row->b_wertung == 0) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_0' );?></option>
		<option value="3" <?php if ($row->b_wertung == 3) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_3' );?></option>
		<option value="4" <?php if ($row->b_wertung == 4) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_4' );?></option>
		</select>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="auf"><?php echo JText::_( 'LEAGUE_UP' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf" id="auf" size="4" maxlength="10" value="<?php echo $row->auf; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_auf"><?php echo JText::_( 'LEAGUE_UP_POSSIBLE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf_evtl" id="auf_evtl" size="4" maxlength="10" value="<?php echo $row->auf_evtl; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="ab"><?php echo JText::_( 'LEAGUE_DOWN' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab" id="ab" size="4" maxlength="10" value="<?php echo $row->ab; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_ab"><?php echo JText::_( 'LEAGUE_DOWN_POSSIBILE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab_evtl" id="ab_evtl" size="4" maxlength="10" value="<?php echo $row->ab_evtl; ?>" />
	</td>
	</tr>
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_BOARD_VALUATION' ); ?></legend>
      <table class="paramlist admintable">
	<tr>
	<td nowrap="nowrap">
	<label for="params['btiebr1']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION1' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr1']" id="params['btiebr1']" value="<?php echo $row->params['btiebr1']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr1'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr2']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION2' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr2']" id="params['btiebr2']" value="<?php echo $row->params['btiebr2']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr2'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params['btiebr3']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION3' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr3']" id="params['btiebr3']" value="<?php echo $row->params['btiebr3']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr3'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr4']"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION4' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr4']" id="params['btiebr4']" value="<?php echo $row->params['btiebr4']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr4'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params['btiebr5']"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN5' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr5']" id="params['btiebr5']" value="<?php echo $row->params['btiebr5']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr5'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params['btiebr6']"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN6' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params['btiebr6']" id="params['btiebr6']" value="<?php echo $row->params['btiebr6']; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params['btiebr6'] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="params['bnhtml']"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_LIST' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params['bnhtml']" id="params['bnhtml']" size="2" maxlength="2" value="<?php echo $row->params['bnhtml']; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="params['bnpdf']"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_PDF' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params['bnpdf']" id="params['bnpdf']" size="2" maxlength="2" value="<?php echo $row->params['bnpdf']; ?>" />
	</td>
	</tr>
	
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_PREFERENCES' ); ?></legend>
      <table class="paramlist admintable">

      <tr>
	<td nowrap="nowrap" colspan="2">
	<label for="params[incl_to_season]"><?php echo JText::_( 'OPTION_INCL_TO_SEASON' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
		<?php echo JHtml::_('select.booleanlist', 'params[incl_to_season]', 'class="inputbox"', $row->params['incl_to_season']); ?>
	</fieldset></td>
	</tr>
	<tr>
	<td nowrap="nowrap" colspan="2">
	<label for="anzeige_ma"><?php echo JText::_( 'LEAGUE_SHOW_PLAYERLIST' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['anzeige_ma']; ?>
	</fieldset></td>
	</tr>

    <tr>
	<td nowrap="nowrap">
	<label for="params['pgntype']"><?php echo JText::_( 'LEAGUE_PGN_TYPE' ); ?></label>
	</td><td colspan="5">
		<select name="params['pgntype']" id="params['pgntype']" value="<?php echo $row->params['pgntype']; ?>" size="1">
		<option value="0" <?php if ($row->params['pgntype'] == 0) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_NO' );?></option>
		<option value="1" <?php if ($row->params['pgntype'] == 1) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_LEAGUE_NAME' );?></option>
		<option value="2" <?php if ($row->params['pgntype'] == 2) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_SHORT_LEAGUE_NAME' );?></option>
		<option value="3" <?php if ($row->params['pgntype'] == 3) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_TEAM_NAMES' );?></option>
		<option value="4" <?php if ($row->params['pgntype'] == 4) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_SHORT_TEAM_NAMES' );?></option>
		<option value="5" <?php if ($row->params['pgntype'] == 5) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_ALL_SHORT_NAMES' );?></option>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params['pgnlname']"><?php echo JText::_( 'LEAGUE_SHORT_NAME' ); ?></label>
	</td><td colspan="5">
	<input class="inputbox" type="text" name="params['pgnlname']" id="params['pgnlname']" size="30" maxlength="30" value="<?php echo $row->params['pgnlname']; ?>" />
	</td>
	</tr>

    <tr>
	<td nowrap="nowrap" colspan="2">
	<label for="mail"><?php echo JText::_( 'LEAGUE_MAIL' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['mail']; ?>
	</fieldset></td>
	</tr>
<?php if ($sl_mail == "1") { ?>
	<tr>
	<td nowrap="nowrap" colspan="2">
	<label for="sl_mail"><?php echo JText::_( 'LEAGUE_MAIL_CHIEF' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['sl_mail']; ?>
	</fieldset></td>
	</tr>
<?php } else { ?>
	<input type="hidden" name="sl_mail" value="0" />
<?php } ?>
	<tr>
	<td nowrap="nowrap" colspan="2">
	<label for="order"><?php echo JText::_( 'LEAGUE_ORDERING' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['order']; ?>
	</fieldset></td>
	</tr>
	<tr>
	<td nowrap="nowrap" colspan="2">
	<label for="published"><?php echo JText::_( 'LEAGUE_PUBLISHED' ); ?></label>
	</td><td colspan="4"><fieldset class="radio">
	<?php echo $lists['published']; ?>
	</fieldset></td>
	</tr>


	</table>
  </fieldset>
  </div>

  <div class="width-40 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'REMARKS' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
	
  </fieldset>
  
    <fieldset>
  	<legend><?php echo JText::_( 'LEAGUE_HINTS' ); ?></legend>
  	<b><?php echo JText::_( 'LEAGUE_HINTS_PAIRING_MODE' ); ?></b>
  	
  	<?php echo JText::_( 'LEAGUE_HINTS_1' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_2' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_3' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_4' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_5' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_6' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_7' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_8' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_9' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_10' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_11' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_12' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_13' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_14' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_15' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_16' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_17' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_18' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_19' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_20' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_21' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_22' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_23' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_24' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_25' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_26' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_27' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_28' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_30' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_31' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_32' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_33' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_34' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_35' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_36' ); ?>
  	</fieldset>


  </div>

<div class="clr"></div>

	<input type="hidden" name="section" value="ligen" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="sid_alt" value="<?php echo $row->sid; ?>" />
	<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="rnd" value="<?php echo $row->rnd; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php }}
?>
