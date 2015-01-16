<?php if (count($answers) > 0): ?>
<ul data-inset="true" data-role="listview" class="feedback-listview listview-white">

  <li>
    <h1>Feedback for appointment with <? echo $answers[0]['coname'] ?> on <? echo $answers[0]['feedback_date'] ?></h1>
    <p>Appointment set by <? echo $answers[0]['bde'] ?>. Feedback left by <? echo $answers[0]['cae'] ?> </p>
    <p>Associated URN: <? echo $answers[0]['urn'] ?></p>
  </li>
</ul>
  <ul data-role="listview" data-inset="true">
    <?php foreach ($answers as $question => $row): ?>
      <li>
        <p><?
          if ($row['question'] == "comments"):
		  echo $questions[$row['question']].": ". $row['reason'];
		  elseif($row['question'] == "quote"):
		  echo $questions[$row['question']];
		   echo ($row['score'] == "0" ? " <span style='color:red'>No<span>:" . $row['reason'] :  " <span style='color:green'>Yes</span>: Actruris Ref " . $row['reason']);
		  else:
            echo $questions[$row['question']]
            ?>: <?
            echo ($row['score'] > 0 ? "<span style='color:green'>Yes</span> (value:" . $row['score'] . ")" : "<span style='color:red'>No<span><p>".$row['reason']."</p>");
          endif;
          ?></p>

      </li>
  <?php endforeach; ?>
      <li> <h2>Total Score <? echo $answers[0]['total'] ?></h2></li>
  </ul>
<? else: ?>
<ul data-inset="true" data-role="listview" class="feedback-listview listview-white">

  <li>
    <h1>No feedback has been left for this appointment</h1>
  </li>
</ul>
<? endif; ?>
</div>