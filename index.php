<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rate an Assignment</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $( function() {
    $( "#accordion" ).accordion();
  });
</script>
<script>
$(document).ready(function(){
//  Check Radio-box
    $(".rating input:radio").attr("checked", false);
    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });

    $('input:radio').change(
    function(){
        var userRating = this.value;
    });
});
</script>
</head>
<body>

<?PHP

if (empty($_POST['launch_presentation_return_url']))
{
        echo "<br /><br /><br /><center>";
	echo "<div style='width: 60%; text-align: left'>";
        echo "<br />This link is only to be used through the Web Link tool in Blackboard.";
	echo "</div></center>";
        die();
}


$url = $_POST['launch_presentation_return_url'];
$query=parse_url($url,PHP_URL_QUERY);
parse_str($query, $out);

$course_id=$out['course_id'];
$course_title=$_POST['context_title'];
$course_batchUID=$_POST['context_label'];
$user_id=$_POST['user_id'];
$name=$_POST['lis_person_name_full'];

preg_match_all('/\//', $url,$matches, PREG_OFFSET_CAPTURE);
$clientURL = substr($url, 0, $matches[0][2][1]);

require_once('classes/Rest.class.php');
require_once('classes/Token.class.php');

$rest = new Rest($clientURL);
$token = new Token();

$token = $rest->authorize();
$access_token = $token->access_token;


$user = $rest->readUser($access_token, "uuid:".$user_id);
// Get user id to use when finding if they are instructor or student
$userid = $user->id;

// Gets the membership of all users in the course
// We use this to find the user's id and get their courseRoleId (instructor or student)
$membership = $rest->readMembership($access_token, $course_id, "");
// Get results so we can loop through them
$m = $membership->results;
$user_status = "";
foreach($m as $row)
{
	if($row->userId == $userid)
	{
		$user_status = $row->courseRoleId;
		break;
	}
}

// Grabs all of the assignments from the course
$columns = $rest->readGradebookColumns($access_token, $course_id);
// Get results so we can loop through them
$c=$columns->results;
echo $user_status . "<br /><br />";
// Use jQuery to load the assignments into their own div block
if ($user_status == "Student") {
 echo "<div id='accordion'>";
 $i = 1;
 foreach($c as $row)
 {
	// We don't want to show Total or Weighted Total because these arent real assignments
        if ($row->name == "Total" || $row->name == "Weighted Total")
        {
		continue;
        } else {
		echo "<h3>" . $row->name . "</h3>";
		echo "<div><p><h4>Assignment ID: " . $row->id . "</h4>";
		echo "<form action='' method='POST' name='form_" . $i . "'>";
		echo "<textarea name='ta" . $i . "' rows='4' cols='50'></textarea>";
		echo "<div class='rating'>";
		echo "<span><input type='radio' name='rating' id='str5' value='5'><label for='str5'></label></span>";
		echo "<span><input type='radio' name='rating' id='str4' value='4'><label for='str4'></label></span>";
    		echo "<span><input type='radio' name='rating' id='str3' value='3'><label for='str3'></label></span>";
    		echo "<span><input type='radio' name='rating' id='str2' value='2'><label for='str2'></label></span>";
    		echo "<span><input type='radio' name='rating' id='str1' value='1'><label for='str1'></label></span>";
		echo "<input type='submit' name='sub" . $i . "' /></form></p></div></div>";
	}
	$i++;
 }
 echo "</div>";
} else {
 echo "<script src='js/Chart.bundle.js'></script>";
 echo "<script src='js/utils.js'></script>";

 echo "<div id='accordion'>";
 $i = 1;
 foreach($c as $row)
 {
        // We don't want to show Total or Weighted Total because these arent real assignments
        if ($row->name == "Total" || $row->name == "Weighted Total")
        {
                continue;
        } else {
                echo "<h3>" . $row->name . "</h3>";
                echo "<div><h4>Assignment ID: " . $row->id . "</h4>"; ?>
        <?PHP echo "<div id='canvas-holder" . $i . "' style='width:18%'>";
           echo "<canvas id='chart-area" . $i . "'></canvas>"; ?>
        </div>
        <script>
            <?PHP echo "var config" . $i . " = {"; ?>
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            1,
                            2,
			    3,
			    4,
			    5,
                        ],
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.yellow,
                            window.chartColors.green,
                            window.chartColors.blue,
                        ],
                        label: 'Dataset 1'
                    }],
                    labels: [
                        '1 Star',
                        '2 Star',
                        '3 Star',
                        '4 Star',
                        '5 Star'
                    ]
                },
                options: {
                    responsive: true
                }
            };

                <?PHP echo "function getChart" . $i . "() {";
	        echo "var ctx". $i . " = document.getElementById('chart-area" . $i ."').getContext('2d');";
                echo "var myPie" . $i . " = new Chart(ctx". $i . ", config" . $i . ");}"; ?>

            var colorNames = Object.keys(window.chartColors);
        </script>
       <?PHP
		$i++;
		echo "</div>";
	    }
 }
 echo "</div>";
 echo "<script>function start() {";
 for($x = 1; $x < $i; $x++) {
   echo "getChart" . $x . "();";
 }
 echo "}";
 echo "window.onload = start();</script>";
}
?>
</body>
</html>
