<div class="geninfo">
    <div class="ui-grid-a">
        <?php foreach ($generalInfo as $key => $info): ?>
            <?php if (!empty($info['value'])): ?>
                <div class="ui-block-a"><?php echo $info['label']; ?></div>
                <div class="ui-block-b"><?php echo $info['value']; ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <a href="#" class="pull-right edit" data-role="button" data-inline="true" data-theme="b" data-mini="true">Edit</a>
    <div class="float-push"></div>
</div>

<form class="geninfo-edit-form hidden">
    <input type="hidden" name="urn" class="urn" value="<?php echo $generalInfo['urn']['value']; ?>">
    <div class="ui-grid-a">
        <div class="ui-block-a">Acturis Ref</div>
        <div class="ui-block-b">
        <input name="acturis" value="<?php echo ($generalInfo['acturis']['value']!="Pending"?$generalInfo['acturis']['value']:""); ?>"/>
        </div>
    
        <div class="ui-block-a">Prospector</div>
        <div class="ui-block-b">
            <select name="prospector" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <?php 
                $this->firephp->log($generalInfo);
                foreach ($generalInfo['prospector']['options'] as $option): ?>
                    <option value="<?php echo $option; ?>" 
                        <?php if ($generalInfo['prospector']['value'] == $option) { echo "selected='selected'"; }?>>
                            <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="ui-block-a">Region</div>
        <div class="ui-block-b">
            <select name="rep_group" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <?php foreach ($generalInfo['rep_group']['options'] as $option): ?>
                    <option value="<?php echo $option; ?>" 
                        <?php if ($generalInfo['rep_group']['value'] == $option) { echo "selected='selected'"; }?>>
                            <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="ui-block-a">Manager / Executive</div>
        <div class="ui-block-b">
            <select name="manager" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <?php foreach ($generalInfo['manager']['options'] as $option): ?>
                    <option value="<?php echo $option; ?>" 
                        <?php if ($generalInfo['manager']['value'] == $option) { echo "selected='selected'"; }?>>
                            <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="pull-right" data-role="controlgroup" data-type="horizontal" data-mini="true">
        <a href="#" data-role="button" data-theme="c" class="cancel">Cancel</a>
        <a href="#" data-role="button" data-theme="b" class="save">Save</a>
    </div>
    <div class="float-push"></div>
</form>


     
     
