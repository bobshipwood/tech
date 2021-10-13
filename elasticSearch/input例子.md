
```
input {
 jdbc {
        jdbc_driver_library => "/usr/share/java/mysql-connector-java.jar"
        jdbc_driver_class => "com.mysql.jdbc.Driver"
        jdbc_connection_string => "jdbc:mysql://10.254.0.6:3306/market"
        jdbc_user => "root"
        jdbc_password => "yuetengyun1983727"
        # 分页
        #jdbc_paging_enabled => "true"
        #jdbc_page_size => "5000"
        statement => "SELECT * from api_market where id > :sql_last_value"
        use_column_value => true
        #tracking_column_type => "numeric"
        #tracking_column_type => "timestamp"(numeric)
        tracking_column => "id"
        schedule => "* * * * *"
        last_run_metadata_path => "/etc/logstash/aaa.yml"
        type => "jdbc"
        # 是否记录上次执行结果，true表示会将上次执行结果的tracking_column字段的值保存到last_run_metadata_path指定的文件中；
        #record_last_run => true
        # 是否清除last_run_metadata_path的记录，需要增量同步时此字段必须为false；
        #clean_run => false
     }
}

output {
  elasticsearch {
    hosts => ["http://10.254.0.17:9200"]
#    index => "%{[@metadata][beat]}-%{[@metadata][version]}-%{+YYYY.MM.dd}"
    index => "test1sql3"
#导入到 es 中的文档 id，这个需要设置成主键，否则同一条记录更新后在 es 中会出现两条记录，%{id} 表示引用 mysql 表中 id 字段的值
#    document_id => "%{id}"
    user => "elastic"
    password => "tryes_123"
  }
}

```

