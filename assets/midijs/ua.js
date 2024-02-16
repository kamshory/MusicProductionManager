
 function userAgent() {
        var e, browser, t;
        var userAgent = navigator.userAgent;
        var browserName = navigator.appName;
        var versionFloat = "" + parseFloat(navigator.appVersion);
        var versionInteger = parseInt(navigator.appVersion, 10);
        if(userAgent.indexOf("Opera") != -1)
        {
            browserName = "Opera";
            versionFloat = userAgent.substring(browser + 6);
            if(userAgent.indexOf("Version") != -1)
            {
                versionFloat = userAgent.substring(browser + 8);
            }
        }
        else if(userAgent.indexOf("MSIE") != -1)
        {
            browserName = "Microsoft Internet Explorer";
            versionFloat = userAgent.substring(browser + 5);
        } 
        else if(userAgent.indexOf("Trident") != -1)
        {
            browserName = "Microsoft Internet Explorer";
            if(userAgent.indexOf("rv:") != -1)
            {
                versionFloat = userAgent.substring(browser + 3);
            }
            else 
            {
                versionFloat = "0.0";
            }
        } 
        else if(userAgent.indexOf("Chrome") != -1)
        {
            browserName = "Chrome";
            versionFloat = userAgent.substring(browser + 7);
        } 
        else if(userAgent.indexOf("Android") != -1)
        {
            browserName = "Android";
            versionFloat = userAgent.substring(browser + 8);
        }
        else if(userAgent.indexOf("Safari") != -1)
        {
            browserName = "Safari";
            versionFloat = userAgent.substring(browser + 7);
            if(userAgent.indexOf("Version") != -1)
            {
                versionFloat = userAgent.substring(browser + 8);
            }
        } 
        else if(userAgent.indexOf("Firefox") != -1)
        {
            browserName = "Firefox";
            versionFloat = userAgent.substring(browser + 8);
        }
        else
        {
            if((userAgent.lastIndexOf(" ") + 1) < userAgent.lastIndexOf("/"))
            {
                e = userAgent.lastIndexOf(" ") + 1;
                browserName = userAgent.substring(browser, e);
                versionFloat = userAgent.substring(browser + 1);
                if(browserName.toLowerCase() == browserName.toUpperCase()) 
                {
                    browserName = navigator.appName;
                    if(versionFloat.indexOf(";") != -1)
                    {
                        versionFloat = versionFloat.substring(0, t);
                    }
                    if(versionFloat.indexOf(" ") != -1)
                    {
                        versionFloat = versionFloat.substring(0, t);
                    }
                    if(versionFloat.indexOf(")") != -1)
                    {
                        versionFloat = versionFloat.substring(0, t);
                    }
                    versionInteger = parseInt("" + versionFloat, 10);
                    if(isNaN(versionInteger))
                    {
                        versionFloat = "" + parseFloat(navigator.appVersion);
                    } 
                    versionInteger = parseInt(navigator.appVersion, 10);
                }
            }
        }
        var ua = new Object;
        ua.browserName = browserName;
        ua.fullVersion = versionFloat;
        ua.majorVersion = versionInteger;
        ua.appName = navigator.appName;
        ua.userAgent = navigator.userAgent;
        ua.platform = navigator.platform;
        return ua;
    }