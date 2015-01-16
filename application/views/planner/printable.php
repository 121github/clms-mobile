<!DOCTYPE html>
<html>
    <head>
        <title>Prospector: Data Enrichment Record</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <style>
            .page-end {
                page-break-after: always;
            }
            .record-id {
                color: #999999;
                width: 100%;
                text-align: right;
            }
            tr.address {
                height: 120px
            }
            tr.empty-row {
                height: 38px;
            }
            .field-col {
                width: 20%;
            }
            .value-col {
                width: 40%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php if (!empty($prospects)): ?>
                <?php $i = 1; foreach ($prospects as $prospect): ?>
                    <div class="record">
                        <h2>
                            Swinton Commercial <small>Data Enrichment Record</small>
                        </h2>
                        <div class="record-id">
                            <i>Record <?php echo $i; ?></i>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="field-col">
                                        Field
                                    </th>
                                    <th class="value-col">
                                        Current Value
                                    </th>
                                    <th class="value-col">
                                        New Value
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Business Name</b></td>
                                    <td>
                                        <?php echo $prospect['coname']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="address">
                                    <td><b>Address</b></td>
                                    <td>
                                        <address>
                                            <?php echo $prospect['p_add1'] . '<br/>'; ?>
                                            <?php echo $prospect['p_add2'] . '<br/>'; ?>
                                            <?php echo $prospect['p_add3'] . '<br/>'; ?>
                                            <?php echo $prospect['p_town'] . '<br/>'; ?>
                                        </address>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Postcode</b></td>
                                    <td>
                                        <?php echo $prospect['p_postcode']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Contact</b></td>
                                    <td>
                                        <?php
                                        if (!empty($prospect['contacts'][0])) {
                                            echo $prospect['contacts'][0]['title'] . " " . $prospect['contacts'][0]['firstname'] . " " . $prospect['contacts'][0]['lastname'];
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Telephone</b></td>
                                    <td>
                                        <?php echo $prospect['contacts'][0]['telephone']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Mobile</b></td>
                                    <td>
                                        <?php echo $prospect['contacts'][0]['mobile']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Email</b></td>
                                    <td>
                                        <?php echo $prospect['contacts'][0]['email']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Trade / Industry</b></td>
                                    <td>
                                        <?php echo $prospect['cotrades']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Employees</b></td>
                                    <td>
                                        <?php echo $prospect['employees']; ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Turnover</b></td>
                                    <td>
                                        <?php echo str_replace('£', '&pound;', $prospect['turnover']); ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="record-id">
                            <i>Record <?php echo $i; ?></i>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Policy</th>
                                    <th>Broker</th>
                                    <th>Insurer</th>
                                    <th>Renewal Date</th>
                                    <th>Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prospect['policies'] as $policy): ?>
                                    <tr>
                                        <td>
                                            <?php echo $policy['type']; ?>
                                        </td>
                                        <td>
                                            <?php echo $policy['broker']; ?>
                                        </td>
                                        <td>
                                            <?php echo $policy['insurer']; ?>
                                        </td>
                                        <td>
                                            <?php echo $policy['date']; ?>
                                        </td>
                                        <td>
                                            <?php echo $policy['premium']; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="empty-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr> 
                                <tr class="empty-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr> 
                                <tr class="empty-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr> 
                            </tbody>
                        </table>
                        <div class="record-id">
                            <i>Record <?php echo $i; ?></i>
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="field-col"><b>Consent to Contact Y/N</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Consent given by</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Date consent given</b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="record-id">
                            <i>Record <?php echo $i; ?></i>
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="field-col"><b>Appointment Agreed</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Name</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Date</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Time</b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="record-id">
                            <i>Record <?php echo $i; ?></i>
                        </div>
                        <table class="table table-bordered page-end">
                            <tbody>
                                <tr>
                                    <td class="field-col"><b>iPad Updated</b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php $i++; endforeach; ?>
            <?php else: ?>
                Nothing to print
            <?php endif; ?>
        </div>
    </body>
</html>
