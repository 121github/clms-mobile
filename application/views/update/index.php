
<ul data-inset="true" data-role="listview" class="update-listview listview-white">
    <?php foreach($response as $k => $v): ?>
      <li><?php echo $v['action'] ?> : <?php echo $v['msg'] ?></li>
      <?php endforeach; ?>
</ul>