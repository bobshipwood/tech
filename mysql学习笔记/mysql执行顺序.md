

### 正常sql语句

select distinct

```
<select_list>
```

from

```
<left_table><join_type>
```

join <right_table> on <join_condition>

where<where_condition>

group by<group_by_list>

having<having_condition>

order by<order_by_condition>

limit<limit_number>

### 执行顺序：

from  <left_table>

on <join_condition>

<join_type> JOIN <right_table>

where <where_condition>

group by <groub_by_list>

having<having condition>

select

distinct<select_list>

order by <order_by_condition>

limit<limit_number>