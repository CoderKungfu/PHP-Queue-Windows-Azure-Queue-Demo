#PHP-Queue Windows Azure Queue Demo

Demo app for Windows Azure Service Bus Queue backend.

Part of my presentation at [The Singapore Xmas Azure Workshop](https://www.facebook.com/events/382258025194568/) on 20 Dec 2012.

##Setup Instructions

###Pre-requisites

1. [Register](http://www.windowsazure.com/en-us/pricing/free-trial/) for a Windows Azure account. You can use the [free 90 days trial here](http://www.windowsazure.com/en-us/pricing/free-trial/).
2. Create a Windows Azure website ([tutorial here](http://www.windowsazure.com/en-us/develop/php/tutorials/website-w-mysql-and-git/)).
3. Create a Windows Azure Service Bus Namespace ([tutorial here](http://www.windowsazure.com/en-us/develop/php/how-to-guides/service-bus-queues/#create-a-service-namespace)).
4. Create a Windows Azure Blob Storage Account ([tutorial here](http://www.windowsazure.com/en-us/develop/php/how-to-guides/blob-service/#header-3)).
5. Download the sample code.

	```
git clone git://github.com/miccheng/PHP-Queue-Windows-Azure-Queue-Demo.git
```
6. Download and install Composer

	```
curl -s "http://getcomposer.org/installer" | php
```

7. Download the Windows Azure SDK & PHP-Queue via Composer.

	```
php composer.phar install
```

###Environment Variables

You have to prepare 2 connection strings:

- `queue_connection_string` - For connecting to your Service Bus Namespace.
- `wa_blob_connection_string` - For connecting to your Blob Storage Account.

_**Refer to the Windows Azure tutorials above to find out how to get these connection string/credentials**_

####Adding to Windows Azure Website

1. Login to the Windows Azure Management Portal
2. Click on the Website that you created.
3. Click on "Configure"
4. Scroll down to "app settings" and add your connection strings.

#### Adding to your bash environment (Linux/OSX)

1. In console, type:

	```
$ export queue_connection_string='Endpoint=<EndpointURL>;SharedSecretIssuer=owner;SharedSecretValue=<SharedSecret>'
```
	```
$ export wa_blob_connection_string='DefaultEndpointsProtocol=https;AccountName=<YourNameSpace>;AccountKey=<AccountKey>'
```

2. To check that your have the info in your environemnt:

	```
$ export $queue_connection_string
```

	It should print out your connection string.

3. To make sure these environment variables are added whenever you boot up or SSH into your machine, edit the `.bash_profile` file in user's home directory.

	```
$ vim ~/.bash_profile
```
	Add:

	```
export queue_connection_string='Endpoint=<EndpointURL>;SharedSecretIssuer=owner;SharedSecretValue=<SharedSecret>'
export wa_blob_connection_string='DefaultEndpointsProtocol=https;AccountName=<YourNameSpace>;AccountKey=<AccountKey>'
```

### Running the Sample Codes

First, publish the sample code to your Windows Azure Website

```
$ git add remote azure https://<username>@<WindowsAzureGitRepo>

$ git push azure master
```


#### Simple Queue

1. REST interface	```curl -XPOST http://<yourwebsitename>.azurewebsites.net/Noob/ -d "var1=foo&var2=bar"```	```curl -XPOST http://<yourwebsitename>.azurewebsites.net/Noob/?REQUEST_METHOD=PUT -d "t=meh"```
2. CLI interface	```$ php cli.php Noob add --data '{"name":"Dino Bing"}'$ php cli.php Noob work```
#### Gallery
1. Visit the gallery website: `http://<yourwebsitename>.azurewebsites.net/gallery/`2. Click on "Upload" to upload a photo.3. Run the Job.
	```
$ php cli.php Photos work
```
4. Go back to the Gallery page and refresh.
5. Your new photo should appear.

#### Running the Photo Worker Daemon

1. Start a Windows Azure Virtual Machine (use the CentOS 6.2 linux distro). Login to your box.
2. Create a new file `install.sh` and use these contents.

	```
#!/bin/sh
sudo yum install -y screen git php php-gd php-pear php-process
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
git clone git://github.com/miccheng/PHP-Queue-Windows-Azure-Queue-Demo.git
cd PHP-Queue-Windows-Azure-Queue-Demo
composer install
```

2. Check-out the Git repository into your Linux box.
3. Change to the `bin` directory.
4. Start the daemon:

	```
$ php daemon.php start
```

5. To stop the daemon:

	```
$ php daemon.php stop
```