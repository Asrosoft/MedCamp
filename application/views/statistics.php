<?php
$this->load->view("header");
$this->load->view("menu");
?>
<h2><?= $title;?></h2>
<?php 
	echo form_open('statistics', array('name'=>'stat_form'));
	echo '<table width="30%"><tr><td align="right">';
	echo form_label('Category:', 'categorySelect');
	echo '</td><td>';
	$js = 'onchange="document.stat_form.submit();"';
	$categories = array(
		'diagnosis' => 'Diagnosis',
		'gender' => 'Gender',
		'race' => 'Race',
		'age' => 'Age',
		'referral' => 'Referrals'
	);
	echo form_dropdown('categorySelect', $categories, $category, $js);
	echo '</td><td><input type="checkbox" name="Referrals" value="1"';
	if ($referrals) {
		echo ' checked ';
	}
	echo ' onclick="document.stat_form.submit();"> Referrals Only';
	echo '</td></tr></table>';
	echo form_close();
?>
    <br />

      <table style="color: #000000;font-family: Arial,Helvetica,sans-serif;font-size: 11px;">
<?php 
	$colWidth = 100 / (count($locations) + 2);
	echo '<tr><th width="'.$colWidth.'%"></th>';
	foreach ($locations as $loc):
		echo '<th width="'.$colWidth.'%" align="right">'.$loc['location'].'</th>';
	endforeach;
	echo '<th width="'.$colWidth.'%" align="right">Total</th></tr>';
	$loc_totals = array();
	foreach ($locations as $loc):
		$loc_totals[$loc['location']] = 0;
	endforeach;
	$line = 0;
	foreach ($statistics as $item):
		$line ++;
		echo "<tr";
		if ($line % 2)
			echo ' style="background-color:#F7F7F7;"';
		echo '><td>'.$item['category'].'</td>';
		foreach ($locations as $loc):
			echo '<td align="right">'.$item[$loc['location']].'</td>';
			$loc_totals[$loc['location']] += $item[$loc['location']];
		endforeach;
		echo '<td align="right">'.$item['total'].'</td></tr>';
	endforeach;
	$line ++;
	echo '<tr';
	if ($line % 2)
		echo ' style="background-color:#F7F7F7;"';
	echo '"><td><b>Total</b></td>';
	$total = 0;
	foreach ($locations as $loc):
		echo '<td align="right"><b>'.$loc_totals[$loc['location']].'</b></td>';
		$total += $loc_totals[$loc['location']];
	endforeach;
	echo '<td align="right"><b>'.$total.'</b></td></tr>';
?>
      </table>

<?php
$this->load->view("footer");
?>
