<?php if (!empty($contacts)): ?>
    <table data-role="table" data-mode="reflow" class="table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th data-priority="1">Priority</th>
                <th data-priority="1">Title</th>
                <th data-priority="1">First Name</th>
                <th data-priority="1">Last Name</th>
                <th data-priority="1"><abbr title="Position">Pos</abbr></th>
                <th data-priority="1"><abbr title="Key Decision Maker">Key DM</abbr></th>
                <th data-priority="1"><abbr title="Telephone Number">Tel</abbr></th>
                <th data-priority="1"><abbr title="Mobile Number">Mob</abbr></th>
                <th data-priority="1">Email</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
                <tr class="contact-row" data-contact_id="<?php echo $contact['id']; ?>">
                    <td><span class="priority"><?php echo ucfirst($contact['priority']); ?></span></td>
                    <td><span class="title"><?php echo $contact['title']; ?></span></td>
                    <td><span class="firstname"><?php echo $contact['firstname']; ?></span></td>
                    <td><span class="lastname"><?php echo $contact['lastname']; ?></span></td>
                    <td><span class="position"><?php echo $contact['position']; ?></span></td>
                    <td><span class="keydm"><?php echo $contact['keydm']; ?></span></td>
                    <td>
                        <a class="telephone" href="tel:<?php echo $contact['telephone']; ?>">
                            <?php echo $contact['telephone']; ?>
                        </a>
                    </td>
                    <td>
                        <a class="mobile" href="tel:<?php echo $contact['mobile']; ?>">
                            <?php echo $contact['mobile']; ?>
                        </a>
                    </td>
                    <td>
                        <a class="email" href="mailto:<?php echo $contact['email']; ?>">
                            <?php echo $contact['email']; ?>
                        </a>
                    </td>
                    <td class="chkbx-fixed">
                        <fieldset data-role="controlgroup" class="chkbx">
                            <input id="contacts-chkbx-<?php echo $contact['id']; ?>"
                                class="contacts-chkbx" type="checkbox" data-iconpos="notext"
                                data-contact_id="<?php echo $contact['id']; ?>">
                            <label for="contacts-chkbx-<?php echo $contact['id']; ?>"></label>
                        </fieldset>
                    </td>
                </tr>
             <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-msg">No contacts have been added for this record</div>
<?php endif; ?>

<div data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right contacts-controls">
    <a href="#" data-role="button" data-theme="b" class="add">Add</a>
    <a href="#" data-role="button" data-theme="b" class="ui-disabled delete">Delete</a>
    <a href="#" data-role="button" data-theme="b" class="ui-disabled edit">Edit</a>
</div>
<div class="float-push"></div>

<form class="contacts-form hidden">
    <input type="hidden" name="urn" class="urn" value="<?php echo $urn; ?>">
    <input name="id" id="contact_id" type="hidden" value="0">
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li data-role="fieldcontain">
            <label for="priority">Priority</label>
            <select name="priority" id="priority" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
            </select>
        </li>
        <li data-role="fieldcontain">
            <label for="title">Title</label>
            <select name="title" id="title" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="Cllr">Cllr</option>
                <option value="Dr">Dr</option>
                <option value="Father">Father</option>
                <option value="Miss">Miss</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Rev">Rev</option>
                <option value="Sister">Sister</option>
            </select>
        </li>
        <li data-role="fieldcontain">
            <label for="firstname">First Name</label>
            <input name="firstname" id="firstname" type="text">
        </li>
        <li data-role="fieldcontain">
            <label for="lastname">Last Name</label>
            <input name="lastname" id="lastname" type="text">
        </li>
        <li data-role="fieldcontain">
            <label for="position">Position</label>
            <select name="position" id="position" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="Director">Director</option>
                <option value="Proprietor">Proprietor</option>
                <option value="Partner">Partner</option>
                <option value="Secretary">Secretary</option>
                <option value="Manager">Manager</option>
                <option value="Other">Other</option>
            </select>
        </li>
        <li data-role="fieldcontain">
            <label for="telephone">Telephone</label>
            <input name="telephone" id="telephone" type="number">
        </li>
        <li data-role="fieldcontain">
            <label for="mobile">Mobile</label>
            <input name="mobile" id="mobile" type="number">
        </li>
        <li data-role="fieldcontain">
            <label for="email">Email Address</label>
            <input name="email" id="email" type="email">
        </li>
        <li data-role="fieldcontain">
            <label for="keydm">Key Decision Maker</label>
            <select name="keydm" id="keydm" data-theme="c">
                <option value="no_selection_made">Please make a selection...</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </li>
        <li>
            <div data-role="controlgroup" data-type="horizontal" data-mini="true" class="pull-right">
                <a href="#" data-role="button" data-theme="c" class="cancel">Cancel</a>
                <a href="#" data-role="button" data-theme="b" class="save">Save</a>
            </div>
            <div class="float-push"></div>
        </li>
    </ul>
</form>