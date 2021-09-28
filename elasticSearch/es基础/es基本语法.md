es基本语法

1 给类型novel加入结构化索引

post    book/novel/_mappings

```
{
	"novel":{
		"properties":{
			"title": {
					"type" : "text"
			  		 }	
		    }
	}
}
```

2 创建结构化的people索引及类型man
put   /people

	{
		"settings":{
			"number_of_shards":3,
			"number_of_replicas":1
		}
		"mappings":{
			"man":{
				"properties":{
	                "name":{
	                    "type":"text"
	                },
	                "country":{
	                    "type":"keyword"
	                },
	                age{
	                    "type":"integer"
	                },
	                date{
	                    "type":"date",
	                    "format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
	                },
				}
			}
		}
	}
