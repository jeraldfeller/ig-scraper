const {Builder, By, Key, until} = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');
const https = require('https');
(async function example() {
    let driver = await new Builder().forBrowser('firefox').build();
    try {
        // login to instagram
        await driver.get('https://www.instagram.com/accounts/login/');
        setTimeout(async function () {
            var email =  await driver.findElement(By.name('username')).sendKeys('sub10.jeraldfeller@gmail.com', Key.RETURN);
            var password =  await driver.findElement(By.name('password')).sendKeys('dfab7c358', Key.RETURN);
            driver.wait(until.elementIsVisible)
            setTimeout(async function () {
                var i = 0;
                while(i < 100000){
                    setTimeout(async function(){
                        var rsp = await scanData(driver)
                        console.log(rsp);
                        i++;
                    }, 1000);
                }
            }, 10000);


        }, 2000)



    } finally {
        // await driver.quit();
    }
})();


async function scanData(driver){
    return new Promise(async function (resolve, reject) {
        try{
            await driver.executeScript('window.open("http://dev.ig-scraper.com/Cron/scan?a=1");');
            setTimeout(async function(){
                driver.getAllWindowHandles().then(async function (handles){
                    await driver.switchTo().window(handles[handles.length - 1]);
                    await driver.findElement(By.id('handle')).click();
                    setTimeout(async function () {
                        // await driver.wait(until.eleme('pre'), 1000)
                        var json = await driver.findElement(By.tagName('pre')).getText();

                        var XMLHttpRequest = require('xhr2');
                        var xhttp = new XMLHttpRequest();
                        xhttp.open("POST", "http://dev.ig-scraper.com/Cron/scan-decode", true);
                        xhttp.setRequestHeader('Content-type', 'application/json');
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {

                                // Response
                                var response = this.responseText;
                                console.log(handles[1]);
                                if(handles.length > 3){
                                    driver.close();
                                }

                                var bol = false;
                                while (bol = false){
                                    try {
                                        bol = true;
                                    }catch (e) {
                                        bol = false;
                                        console.log(e);
                                    }

                                    driver.getAllWindowHandles().then(async function (handles){
                                        await driver.switchTo().window(handles[handles.length - 1]);

                                    });
                                }

                                resolve(response);
                            }
                        };
                        var data = JSON.parse(json);
                        xhttp.send(JSON.stringify(data));

                    }, 1000);

                })

            }, 500);
        }catch (e) {
            driver.getAllWindowHandles().then(async function (handles){
                await driver.switchTo().window(handles[handles.length - 1]);

            });
            resolve('Retry');

        }

    });

}


