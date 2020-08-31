

1、**FROM** table1 left join table2 on 将table1和table2中的数据产生笛卡尔积，生成Temp1

2、**JOIN** table2 所以先是确定表，再确定关联条件

3、**ON** table1.column = table2.columu 确定表的绑定条件 由Temp1产生中间表Temp2

4、**WHERE** 对中间表Temp2产生的结果进行过滤 产生中间表Temp3

5、**GROUP BY** 对中间表Temp3进行分组，产生中间表Temp4

6、**HAVING** 对分组后的记录进行聚合 产生中间表Temp5

7、**SELECT** 对中间表Temp5进行列筛选，产生中间表 Temp6

8、**DISTINCT** 对中间表 Temp6进行去重，产生中间表 Temp7

9、**ORDER BY** 对Temp7中的数据进行排序，产生中间表Temp8

10、**LIMIT** 对中间表Temp8进行分页，产生中间表Temp9