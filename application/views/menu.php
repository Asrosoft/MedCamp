	<div><span style="float:left">
		<a href='<?php echo site_url('medcamp/patients')?>'>Patients</a> |
		<a href='<?php echo site_url('medcamp/statistics')?>'>Statistics</a>
		</span><span style="float:right">
                <a href='<?php echo site_url('medcamp/backup')?>'>Backup</a> |
                <a href='<?php echo site_url('medcamp/diagnosis')?>'>Diagnosis</a> |
		<a href='<?php echo site_url('medcamp/locations')?>'>Locations</a> |
		<a href='<?php echo site_url('medcamp/races')?>'>Races</a> |
                <a href='<?php echo site_url('medcamp/hospitals')?>'>Hospitals</a> |
                <a href='<?php echo site_url('medcamp/departments')?>'>Departments</a> |
		<?php
		$currentYear = date("Y");
		if (isset($_COOKIE["currentyear"])) {
			$year = $_COOKIE["currentyear"];
		} else {
			$year = $currentYear;
		}
		?>
        Current year: <select id='yearList' onchange="selectYear();">
        <?php
        for  ($yr = $currentYear; $yr > 2011; $yr--) {
        	echo "<option value='$yr'";
        	if ($yr == $year) {
        		echo " selected";
        	}
        	echo ">$yr</option>";
        }
        ?>
        </select>
		<?php
		$page = $this->uri->segment(count($this->uri->segments));
		$action = $this->uri->segment(count($this->uri->segments)-1);
		if ($page == "patients"
		|| ($page == 'add' && $action == 'patients')) {
			if (isset($_COOKIE["currentlocation"])) {
				$location = $_COOKIE["currentlocation"];
			} else {
				$location = 0;
			}
		?>
         | Location: <select id='locationList' onchange="selectLocation();">
        <?php
	        $result = $this->Medcamp_model->locations($year);
	        echo "<option value='0'";
	        if ($location == 0) {
	        	echo " selected";
	        }
	        echo ">All</option>";
	        foreach ($result as $loc):
	        	echo "<option value='".$loc["id"]."'";
	        	if ($loc["id"] == $location) {
	        		echo " selected";
	        	}
	        	echo ">".$loc["location"]."</option>";
	        endforeach;
		}
        ?>
        </select> |
		<a href='<?php echo 'http://'.$_SERVER['SERVER_ADDR'];?>'>Close</a>
		</span>
	</div>
	<br />
	<script>
function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value + "; path=/";
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

function selectYear() {
    var selectYear = $('#yearList')[0].value;
    setCookie("currentyear", selectYear, 3);
    setCookie("currentlocation", "0", 3);
    location.reload(true);
}

function selectLocation() {
    var selectLocation = $('#locationList')[0].value;
    setCookie("currentlocation", selectLocation, 3);
    $('form').submit();
}

//$(document).ready(function() {
//    $('#yearList')[0].value = getCookie("currentyear");
//})
</script>
