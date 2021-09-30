```
input {
  jdbc {
    jdbc_driver_library => "mysql-connector-java-5.1.36-bin.jar"
    jdbc_driver_class => "com.mysql.jdbc.Driver"
    jdbc_connection_string => "jdbc:mysql://localhost:3306/mydb"
    jdbc_user => "mysql"
    jdbc_password => "mysql"
    schedule => "* * * * *"
    #parameters => {"favorite_artist" => "Beethoven"}
    #statement => "SELECT * from songs where artist = :favorite_artist"
    statement => "SELECT * from ats_sales_report where last_modified >= :sql_last_value"
    use_colunm_value => true
    tracking_column_type => "timestamp"(numeric)
    tracking_column => "last_modified"
    last_run_metadata_path => "syncpoint_table"
  }
}
output{
	elasticsearch {
		hosts => ["127.0.0.1:9200"]
		#user =>
		#password =>
		index => "sales"
		document_id => "%{id}"
	}
}
```

jdbc_driver_library: jdbc mysql 驱动的路径，(yum install mysql-connector-java  rpm -ql mysql-connector-java)(/usr/share/java/mysql-connector-java.jar)
jdbc_driver_class: 驱动类的名字，mysql 填 com.mysql.jdbc.Driver 就好了
jdbc_connection_string: mysql 地址
jdbc_user: mysql 用户
jdbc_password: mysql 密码
schedule: 执行 sql 时机，类似 crontab 的调度
statement: 要执行的 sql，以 “:” 开头是定义的变量，可以通过 parameters 来设置变量，这里的 sql_last_value 是内置的变量，表示上一次 sql 执行中 update_time 的值，这里 update_time 条件是 >= 因为时间有可能相等，没有等号可能会漏掉一些增量
use_column_value: 使用递增列的值
tracking_column_type: 递增字段的类型，numeric 表示数值类型, timestamp 表示时间戳类型
tracking_column: 递增字段的名称，这里使用 update_time 这一列，这列的类型是 timestamp
last_run_metadata_path: 同步点文件，这个文件记录了上次的同步点，重启时会读取这个文件，这个文件可以手动修改
hosts: es 集群地址
user: es 用户名
password: es 密码
index: 导入到 es 中的 index 名，这里我直接设置成了 mysql 表的名字
document_id: 导入到 es 中的文档 id，这个需要设置成主键，否则同一条记录更新后在 es 中会出现两条记录，%{id} 表示引用 mysql 表中 id 字段的值
