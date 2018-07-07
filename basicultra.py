import RPi.GPIO as GPIO
import time
import os
import MySQLdb
from time import strftime
import datetime

GPIO.setmode(GPIO.BOARD)
TRIGGER=18
ECHO=24
GPIO.setup(TRIGGER,GPIO.OUT)
GPIO.setup(ECHO,GPIO.IN)
def distance():
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
    return round(Distance,2)
if __name__=="__main__":
        while True:
            dist=distance()
            print("Distance is "+str(dist))
            time.sleep(2)
