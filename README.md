# CFRatingColor
利用Shields.io，实现按颜色显示Codeforces的Rating
## 现成的API
国际惯例，先丢API：[http://cfrating.ihcr.top](http://cfrating.ihcr.top)


## 使用方法
~~**访问数过多，会造成限制导致蹦Unrated！暂时无解**~~    
多次检测，麻麻再也不用担心假Unrated了！



以下是两个示例：**由于启用了longCache，第一次调用时可能较慢**    
[https://cfrating.ihcr.top/?user=tourist](https://cfrating.ihcr.top/?user=tourist)    
![https://cfrating.ihcr.top/?user=tourist](https://cfrating.ihcr.top/?user=tourist)    
[https://cfrating.ihcr.top/?user=YangYunFei&style=flat-square](https://cfrating.ihcr.top/?user=YangYunFei&style=flat-square)    
![https://cfrating.ihcr.top/?user=YangYunFei&style=flat-square](https://cfrating.ihcr.top/?user=YangYunFei&style=flat-square)  


参数如您所见：   


user：您想要获取的用户    
style：Shields.io的主题，可以不填，默认是for-the-badge   
**更多主题字符串可去[https://shields.io](https://shields.io)查看！**  

**新版更新资瓷了缩写：**
使用st函数传入，不可与style传入重用。  
目前手工定义：  
- f1 -> flat
- f2 -> flat-square



## 在自己服务器上搭建
就一个php文件，clone下来用就行。

## 原理
使用Codeforces的user.info API来获取用户的Rating。    
之后判断好颜色，然后302到Shields.io，输出svg图片。   
 
 
**由于本API依赖以上两个服务，因此上述服务中任意一个出现问题，该API将无法使用！**

## 最后
这是我从深夜肝到凌晨的成果，先在此求个**star**先。  
该API的运行离不开Codeforces和Shields.io的API提供，向他们致以诚挚的感谢。


**感谢YangYunFei(@Anguei)，协助我找出了80%的bug。**  
~~（项目一上线就修bug，真实）~~
