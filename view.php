<style>
    div,
    p {
        text-align: center;
    }

    input {
        height: 30px;
        width: 300px;
    }

    input[type=submit] {
        margin-top: 20px;
    }

    input[type=text],
    select {
        width: 30%;
        padding: 12px 20px;
        margin: 8px 0;

        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type=submit],
    input[type=reset] {
        width: 10%;
        height: 50px;
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type=submit]:hover {
        background-color: #45a049;
    }

    form {
        margin-top: 50px;
    }

    #person,
    label,
    p {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #person td,
    #person th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #person tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #person tr:hover {
        background-color: #ddd;
        cursor: pointer;
    }

    #person th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<?php

/*** db config */
include 'vendors/db.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'allmodern_db';
/*** end */
$db = new db($dbhost, $dbuser, $dbpass, $dbname);

if (isset($_POST['name']) && $_POST['name'] != '') {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $in_stock = $_POST['stock'];
    $shipping = $_POST['shipping'];
    $category = $_POST['name'];

    if ($_POST['btnsubmit'] == 'Submit') {
        $db->query('INSERT INTO tb_scrape (`name`,`price`,`in_stock`,`shipping`, `category`) VALUES (?,?,?,?,?)', "$name", "$price", "$in_stock", "$shipping", "$category");
    } elseif ($_POST['btnsubmit'] == 'Edit') {
        $id = $_POST['id'];
        $db->query('UPDATE tb_scrape SET `name` = ?, `price` = ?, `in_stock` = ? , `shipping` = ?, `category` = ? WHERE `id` = ?', "$name", "$price", "$in_stock", "$shipping", "$category", "$id");
    } elseif ($_POST['btndelete'] == 'Delete') {
        $id = $_POST['id'];
        $db->query('DELETE FROM tb_scrape WHERE `id` = ?', "$id");
    }
}

// get all data
$list = $db->query('SELECT * FROM tb_scrape')->fetchAll();

// show all distinct categories
$categories = $db->query('SELECT DISTINCT category FROM tb_scrape')->fetchAll();

/*** pagination */
if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 10;
$offset = ($pageno - 1) * $no_of_records_per_page;

$total_rows = count($list);
$total_pages = ceil($total_rows / $no_of_records_per_page);

$sql = "SELECT * FROM tb_scrape LIMIT $offset, $no_of_records_per_page";
$res_data = $db->query($sql)->fetchAll();

?>
    <?php include('inc/header.html'); ?>
    <?php if (count($list) > 0) : ?>
        <table id="person">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Shipping</th>
                <th>Category</th>
            </tr>
            <?php foreach ($res_data as $data) : ?>
                <tr>
                    <?php foreach ($data as $k => $obj) : ?>
                        <td><?php echo $data[$k]; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <div>
        <?php if (count($res_data) > 0) : ?>
            <p style="color: #0000ff;font-size:x-large;padding: 0;margin: 0;">Click on the the table row to <span style="color: red;">EDIT</span> a record!</p>
        <?php endif; ?>

        <ul class="pagination" style="padding: 0;margin: 0;padding-top:5px;">
            <li><a href="?pageno=1">First</a></li>
            <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
            </li>
            <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
        </ul>

        <form name="tform" id="tform" action="view.php" method="POST" style="padding: 10;margin: 0;">
            <input type="hidden" id="id" name="id">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name"><br>
            <label for="price">Price:</label><br>
            <input type="text" id="price" name="price"><br>
            <label for="email">Stock:</label><br>
            <input type="text" id="stock" name="stock"><br>
            <label for="shipping">Shipping:</label><br>
            <input type="text" id="shipping" name="shipping"><br>
            <label for="category">Category:</label><br>
            <select name="category" id="category">
                <?php foreach ($categories as $k => $category) : ?>
                    <option value="<?php echo $category['category']; ?>"><?php echo $category['category']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <input type="submit" id="btnsubmit" name="btnsubmit" value="Submit" /> | <input type="submit" id="btndelete" name="btndelete" value="Delete" /> | <input type="reset" id="reset" value="Reset" />
        </form>
        <div />
        <script>
            $("#person tr").click(function() {
                var datastring = $("#tform").serialize();
                $(this).closest('tr').find('td').each(function(i) {
                    var $inputs = $('#tform :input');
                    var textval = $(this).text();
                    $inputs[i].value = textval;
                });
                $("#btnsubmit").val("Edit");
            });
            $("#reset").on("click", function() {
                $("#btnsubmit").val("Insert");
            });
        </script>
        <?php include('inc/footer.html'); ?>