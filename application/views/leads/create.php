<form class="create-form">
    <ul data-inset="true" data-role="listview" class="listview-white">
        <li>
            Please enter the company details below to create a new prospect record.
        </li>
        <li><label for="coname">Company Name</label>
            <input name="coname" id="coname" type="text" required>
        </li>
        <li><label for="postcode">Postcode</label>
            <input name="postcode" id="postcode" type="text" required>
        </li>
        <li><label for="telephone">Telephone Number</label>
            <input name="telephone" id="telephone" type="text">
        </li>
        <li>
            <button data-theme="b" class="create">Create</button>
        </li>
    </ul>
</form>
<div id="dupes" class="hidden">
<ul data-inset="true" data-role="listview" class="listview-white">
        <li>
            The following records were found with matching details...

 <table data-role="table" data-mode="reflow" class="dupe-table table-stroke table-stripe responsive-table">
        <thead>
            <tr>
                <th data-priority="1">System</abbr></th>
                <th data-priority="1">URN/ACTURIS REF</th>
                <th data-priority="1">Company Name</th>
                <th data-priority="1">Last Update</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
 </table>
                    </li>
</ul>
</div>