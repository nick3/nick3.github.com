---
layout: post
title: "Mac在GitHub上搭建Octopress的过程记录"
date: 2012-01-26 14:54
comments: true
categories: [Mac, Octopress, GitHub]
---
网上同类文章很多了，我只是把自己的搭建过程记录一下。

早先我也曾经尝试搭过一次，但是遇到了一些问题，安装ruby1.9.2，在编译的时候老是报错。和我本机装的是Xcode 4.2可能有关系。在网上查了一下解决方案，说是可以通过下面的命令来安装ruby 1.9.3来代替：
``` bash
rvm install 1.9.3 --with-gcc=clang
```
但是我试过之后还是在make阶段报错。无奈只能重装Xcode 4.1。
***
下面开始正篇：

参考文章当然是官方文档：
[Octopress Documentation](http://octopress.org/docs/)

写得很详细了，初心者按步骤做应该也可以搭建成功。


## 1. 准备工作
我这里的软件环境是 Mac OS X 10.7.2、Xcode 4.1。文本编辑器用的 [Sublime Text 2](http://www.sublimetext.com/2) ，当然神器 [Textmate](http://macromates.com/) 也是完全OK的，但因为中文问题所以暂时用前者代替，等2.0出来了再换。另外推荐大家使用 [iTerm2](http://www.iterm2.com/) 来代替系统自带的终端。

还有一点要说的是，要在能顺利访问国外网站的网络环境里面（不管你是人肉翻，还是ＶＰＮ翻，或是GAE翻，或是Tor翻，或是某界翻，或是某门翻…）。因为众所周知的原因，和ruby相关的部分网站是被认证的（其实被认证的是amazon的服务器）。

### 1.1 Git
系统自带了Git，版本是1.7.4.4，我先去 [官网](http://git-scm.com/) 将它更新到最新。直接下载安装包安装。

### 1.2 RVM
[RVM(Ruby Version Manager)](https://rvm.beginrescueend.com/) 是用来管理ruby及其环境的工具，安装也非常简单：
``` bash
bash -s stable < <(curl -s https://raw.github.com/wayneeseguin/rvm/master/binscripts/rvm-installer)
```

### 1.3 Ruby
安好RVM后就可以安装Ruby了， 系统自带的Ruby版本是1.8.7，但是Octopress要求的是1.9.2，so…
``` bash
rvm install 1.9.2 && rvm use 1.9.2
```


OK, 如果上面的准备工作都做完了，那么就可以正式开始安装Octopress了！


## 2. 开始安装Octopress
首先找个目录来存放Octopress吧。在终端中打开那个目录后输入下面的命令：
``` bash
git clone git://github.com/imathis/octopress.git octopress
cd octopress    # If you use RVM, You'll be asked if you trust the .rvmrc file (say yes).
gem install bundler
bundle install
rake install
```

至此，你本地的Octopress就已经安装完毕了。


## 3. 部署到GitHub
官方提供的部署文档里面写了GitHub Pages、Heroku以及Rsync的部署方法。我这里选择了 [GitHub Pages](http://pages.github.com/)，使用的是GitHub User pages。

由于GitHub使用SSH公钥来进行授权，系统中的每个用户都必须提供一个公钥用于授权，没有的话就要去生成一个。

先检查一下是不是已有key：
``` bash
cd ~/.ssh
ls
```
如果有以pub为后缀的文件，则为已有的key。备份一下：
``` bash
mkdir key_backup
mv id_rsa* key_backup
```
生成SSH key：
``` bash
ssh-keygen -t rsa -C imjustanemail@gmail.com # 这里所输入的email是你自己的一个帐号名称
```
这里会要你输入要保存的文件名与一个通行码，直接回车就可以了。生成完后应该会有一个叫做id_rsa.pub的文件，使用任意的文本编辑器打开这个文件，并将其全部内容复制出来。

登录到GitHub，进入Account Settings -> SSH Public Keys -> Add another key，将刚刚复制的内容粘贴到Key下面的输入框中，在Title下面的输入框里填写刚刚命令里面所输入的帐号名称，并保存。

然后是建立一个名为*username*.github.com的repo。由于对GitHub Pages不熟悉，我在这个步骤上花费了很多时间。GitHub官方页面上给出的建立方法里面并没有将这个 **username** 进行斜体处理，或进行额外说明：

![GitHub pages](http://nick3-wordpress.stor.sinaapp.com/uploads/2012/01/githubpage.jpg)

导致我一直以为就是建立一个叫做username.github.com的repo……(-_-!)，经过若干次失败后，我脑袋终于开窍了，原来这里的 **username** 是tm我自己的用户名啊！

建立好这个repo之后，就可以开始部署Octopress了。

回到octopress本地所在的目录，输入下面的命令：
``` bash
rake setup_github_pages
```
会询问你GitHub Pages repo的url，也就是刚刚建立的那个repo的ssh url。然后在执行：
``` bash
rake generate
rake deploy
```
到这里，Octopress已经部署好了，应该可以通过 *username*.github.com 这个地址看到页面了（或许要稍等几分钟，GitHub会发邮件通知你）。

如果你要绑定域名的话：
``` bash
echo 'www.mycustomdomain.com' >> source/CNAME # 前面是你自己的域名
```
然后去你的域名提供商那里设定新增CNAME。

最后别忘了提交源代码：
``` bash
git add .
git commit -m '写点提交注释'
git push origin source
```

搞定，收工！