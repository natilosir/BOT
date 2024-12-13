<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @font-face {
            font-family: 'CustomFont';
            src: url('https://dl.natilos.ir/ffff/FiraCode-Medium.woff2') format('woff');
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'CustomFont', 'Arial', sans-serif;
            background-color: #1e1e2f;
            color: #ffffff;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            border-collapse: collapse;
            color: #ffffff;
            table-layout: auto;
        }

        table thead {
            background-color: #6a0dad;
            color: #ffffff;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #444;
            font-size: 12px;
        }

        table tbody tr:nth-child(odd) {
            background-color: #3b3b51;
        }

        table tbody tr:nth-child(even) {
            background-color: #2e2e42;
        }

        table tbody tr:hover {
            background-color: #5e4a82;
            transition: 0.2s;
        }

        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #ff1a1a;
        }
#success-message {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    font-size: 16px;
}

        @media (max-width: 768px) {
            table {
                width: 900px;
            }
        }
    </style>
</head>


<body>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Delete</th>
                    <?php
                    include 'ORM/ORM.php';
                    $table = isset($_GET['db']) ? $_GET['db'] : 'users';
                    $Order = isset($_GET['order']) ? $_GET['order'] : 'asc';
                    $by    = isset($_GET['by']) ? $_GET['by'] : 'id';
                    $data  = DB::table($table)->orderby($by, $Order)->get();
                    if (! empty($data)) {
                        foreach (array_keys((array) $data[0]) as $column) {
                            echo "<th>{$column}</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // نمایش داده‌ها در جدول
                foreach ($data as $row) {
                    echo "<tr data-id='{$row->id}'>";
                    echo "<td><button class='delete-btn'>Delete</button></td>";
                    foreach ((array) $row as $column => $value) {
                        echo "<td class='editable' data-column='{$column}' data-id='{$row->id}'>{$value}</td>";
                    }
                    echo '</tr>';
                }
                    ?>
            </tbody>
        </table>
    </div>

    <!-- پیام موفقیت -->
<div id="success-message">Successful</div>

<script>
    $(document).ready(function () {
        // قابلیت حذف
        $('.delete-btn').on('click', function () {
            var row = $(this).closest('tr');
            var id = row.data('id');
            var table = '<?php echo $table; ?>';

            if (confirm('آیا مطمئن هستید که می‌خواهید این رکورد حذف شود؟')) {
                $.ajax({
                    url: 'dbset.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        table: table,
                        id: id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            showSuccessMessage();
                            row.remove();
                        } else {
                            alert('خطا در حذف رکورد: ' + response.error);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('خطا در برقراری ارتباط با سرور.');
                    }
                });
            }
        });

        // قابلیت ویرایش
        $('.editable').on('click', function () {
            var cell = $(this);
            var oldValue = cell.text();
            var column = cell.data('column');
            var id = cell.data('id');

            if( oldValue ){
                var input = $('<input>', {
                    type: 'text',
                    value: oldValue,
                    size: 8,
                });
            }
            

                if ($('input').length === 0) { 
                var input = $('<input>', {
                    type: 'text',
                    value: oldValue,
                    size: 8,
                });
                }
            
                cell.html(input);
                input.focus();

            input.on('blur', function () {
                var newValue = input.val();
                if (newValue !== oldValue) {
                if(newValue==''){newValue='0d5cze8'}                    
                    $.ajax({
                        url: 'dbset.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            table: '<?php echo $table; ?>',
                            id: id,
                            column: column,
                            value: newValue
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                if(newValue=='0d5cze8'){newValue=''}
                                cell.html(newValue); 
                                showSuccessMessage();
                            } else {
                                cell.html(oldValue);
                                alert('خطا در به‌روزرسانی: ' + response.error);
                            }
                        },
                        error: function (xhr, status, error) {
                            cell.html(oldValue); 
                            alert('خطا در برقراری ارتباط با سرور.');
                        }
                    });
                } else {
                    cell.html(oldValue); 
                }
            });
        });

        function showSuccessMessage() {
            $('#success-message').fadeIn().delay(3000).fadeOut();
        }
    });
</script>

</body>
</html>