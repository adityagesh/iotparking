#!/usr/bin/python2
import RPi.GPIO as GPIO
import time
import os
import MySQLdb
from time import strftime
import datetime

#TRIGGER1=18
#ECHO1=24

#TRIGGER2=16
#ECHO2=12

#TRIGGER3=13
#ECHO3=15
os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')
db=MySQLdb.connect(host="localhost",user="root",passwd="root",db="PARKING") #DB CONNECT
cur=db.cursor()
GPIO.setmode(GPIO.BOARD)
def distance(TRIGGER,ECHO):
    flag=0
    StartTime=time.time()
    StopTime=time.time()
    GPIO.output(TRIGGER,True)
    time.sleep(0.0001)
    GPIO.output(TRIGGER,False)
    while GPIO.input(ECHO)==0:
        StartTime=time.time()
    while GPIO.input(ECHO)==1:
        StopTime=time.time()
    ElapsedTime=StopTime-StartTime
    Distance=(ElapsedTime*34300)/2
    print(Distance)
    if round(Distance,2)<=6:
            flag=1 
    return flag;
def status(TRIGGER,ECHO,i):
    GPIO.setup(TRIGGER,GPIO.OUT)
    GPIO.setup(ECHO,GPIO.IN)
    flag=distance(TRIGGER,ECHO)
    sql="UPDATE STATUS SET flag={} WHERE i={};".format(flag,i)
    st=cur.execute(sql)
    print(sql)
    db.commit()
    print("Write Complete")

def lighting(pinG,i):
	sql="SELECT flag,bookflag FROM STATUS WHERE i={};".format(i)
	st=cur.execute(sql)
	data=cur.fetchall()
	GPIO.setup(pinG,GPIO.OUT)
	if(data[0][0] or data[0][1]):
		GPIO.output(pinG,False)
		print(data[0][0])
	else:
		GPIO.output(pinG,True)
		
#3A : GREEN: 40     3
#2A : GREEN: 31     2
#1A : GREEN: 11     1
#3B : GREEN: 7      8
#2B : GREEN: 29     7
#1B : GREEN: 37     6

    
if __name__=="__main__":
    try:
        while True:
            status(18,24,1)
            status(16,12,2)
            status(13,15,3)
            lighting(40,3)
            lighting(31,2)
            lighting(11,1)
            lighting(7,8)
            lighting(29,7)
            lighting(37,6)
            time.sleep(1)
    except KeyboardInterrupt:
            print("User Interrupt")
            GPIO.cleanup()
            db.rollback()
            cur.close()
            db.close()
