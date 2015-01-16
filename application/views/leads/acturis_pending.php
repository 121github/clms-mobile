<?php if (!empty($leads)): ?>

    <ul data-role="listview" data-inset="true">
        <?php foreach ($leads as $lead): ?>
            <li class="result">
                <a href="detail/<?php echo $lead['urn']; ?>" class="hreflink" id="<?php echo $lead['urn']; ?>">
                    <h2><?php echo $lead['coname']; ?></h2>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php else: ?> 

    <ul data-inset="true" data-role="listview" class="listview-white">
        <li>
            There are currently no leads pending acturis reference. 
        </li>
    </ul>

<?php endif; ?>