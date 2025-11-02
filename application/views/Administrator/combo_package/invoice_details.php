
<div id="salesInvoiceReport" class="row">

  <table class="table table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th colspan="4" style="text-align:center">
                Package Name : <?php echo $package_master->packageName; ?> <br/>
            </th>
        
        </tr>
        <tr>
            <th>Date</th>
            <th>Product Name</th>
            <th>Sales Rate</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($package_data as $sale) { ?>
            <tr>
                <td><?= $sale->Product_Name . ' (' . $sale->Product_Code . ')'; ?></td>
                <td><?= date('Y-m-d h:i A', strtotime($sale->add_time));  ?></td>
                <td><?= $sale->selling_price; ?></td>
                <td><?= $sale->qty; ?></td>
              
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>



