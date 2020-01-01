rem *******************************Code Start*****************************
@echo off



set y=%date:~,4%

set m=%date:~5,2%

set d=%date:~8,2%

cd D:\mysql56\bin\
mysqldump --opt -urifeng -puweb@#888rifengaq rifeng > D:\数据库自动备份\rifeng_%y%%m%%d%.sql

forfiles /p D:\数据库自动备份 /s /m *.sql /d -10 /c "cmd /c del @file"





@echo on
rem *******************************Code End*****************************
