<?php if (isset($_SESSION['file_error'])): ?>
    <div class="error">
        <?php 
            echo $_SESSION['file_error']; 
            unset($_SESSION['file_error']); 
        ?>
    </div>
<?php endif; ?>

<?php if(!empty($documents)): ?>
    <?php echo form_open('file/download', array('data-ajax' => 'false')); ?>
        <input type="hidden" name="urn" value="<?php echo $urn; ?>">
        <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
            <thead>
                <tr>
                    <th data-priority="1">Filename</th>
                    <th data-priority="1">File Type</th>
                    <th data-priority="1">Size</th>
                    <th data-priority="1"></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; foreach ($documents as $doc): ?>
                    <?php 
                        $info     = pathinfo($doc['name']);
                        $filename = $info['filename'];
                        $ext      = $info['extension'];
                        $name     = strlen($filename) > 25 
                            ? '<abbr title="' . $doc['name'] . '">' . substr($filename, 0, 25) . '....' . $ext . '<abbr/>' 
                            : $doc['name'];
                    ?>
                    <tr>
                        <td><?php echo $name; ?></td>
                        <td><div class="doc-icon <?php echo $ext; ?>"></div></td>
                        <td><?php echo round($doc['size'] / 1000) . ' kB'; ?></td>
                        <td class="chkbx-fixed">
                            <fieldset data-role="controlgroup" class="chkbx">
                                <input id="file-chkbx<?php echo $i; ?>" name="file-chkbx<?php echo $i; ?>"
                                    type="checkbox" class="file-chkbx" data-iconpos="notext" value="<?php echo $doc['name']; ?>">
                                <label for="file-chkbx<?php echo $i; ?>"></label>
                            </fieldset>
                        </td>
                    </tr>
                <?php $i++; endforeach; ?>
            </tbody>
        </table>
        <div data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right file-ctrls">
            <button data-theme="b" type="submit" class="download" disabled>Download</button>
            <button data-theme="b" type="button" class="delete" disabled>Delete</button>
        </div>
        <div class="float-push"></div>
    </form>
<?php else: ?>
    <div class="empty-msg">No documents/attachments have been added for this record</div>
<?php endif; ?>

<?php echo form_open_multipart('file/upload', array('data-ajax' => 'false', 'onsubmit' => "$.mobile.loading('show')")); ?>
    <input type="hidden" name="urn" value="<?php echo $urn; ?>">
    <input type="file" name="userfile">
    <input type="submit" value="Upload" data-theme="b">
</form>