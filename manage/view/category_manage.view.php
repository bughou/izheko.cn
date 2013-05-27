<table>
<tbody id="category_tbody">
<tr>
    <th>ID</th>
    <th>CID</th>
    <th>名称</th>
    <th>父CID</th>
    <th>is_parent</th>
    <th>爱折扣分类</th>
    <th>创建时间</th>
    <th>更新时间</th>
</tr>
<?php foreach($categories as $category) { ?>
<tr>
    <td><?= $category['id'] ?></td>
    <td><?= $category['cid'] ?></td>
    <td><?= $category['name'] ?></td>
    <td><?= $category['parent_cid'] ?></td>
    <td><?= $category['is_parent'] ? '是' : '' ?></td>
    <td>
        <select class="type_select">
<?php
$type_id = $category['type_id'];
if ($type_id > 0)
    $type_name = isset($types[$type_id]) ? $types[$type_id] : "未知ID: $type_id";
else $type_name = '';

echo <<<EOT
            <option value="$type_id">$type_name</option>
EOT;
foreach($types as $key => $name) {
    if ($key != $type_id) echo <<<EOT
            <option value="$key">$name</option>
EOT;
}
?>
        </select>
    </td>
    <td><?= $category['create_time'] ?></td>
    <td><?= $category['update_time'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<div class="pagination">
<?php
require_once APP_ROOT . '/../common/helper/page.helper.php';
echo paginate('/category_manage.do', $page, $total_count, $page_size);
?>
</div>

<script>
$(function(){
    $('select.type_select').change(function(){
        var $this = $(this);
        if($this.next('button.save_type').length <= 0)
            $this.after('<button class="save_type">保存</button>');
        $this.next('button.save_type').attr('disabled', false);
    });

    $('#category_tbody').on('click', 'button.save_type', function(){
        var $this = $(this);
        $.post(location.pathname, {
            'update': $.trim($this.parent('td').siblings('td:first-child').text()),
            'type_id': $.trim($this.siblings('select.type_select').val()),
        }, function(response){
            if(response === 'ok') $this.attr('disabled', true);
            else alert(response);
        }, 'text');
    });
});
</script>