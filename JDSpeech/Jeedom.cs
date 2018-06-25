using System;
using System.Net.Http;

namespace JDSpeech
{
    class Jeedom
    {
        private HttpClient client;
        private string urlapi = "";
        private string uid = "";

        public Jeedom(string url, string jduid)
        {
            urlapi = "http://" + url + "/plugins/avatar/core/api/avatar.api.php?";
            uid = jduid;
            client = new HttpClient();

        }
        public void Dispose()
        {
            client.Dispose();
        }

        public string send(string cmds,string type)
        {
            string urlreq = urlapi + "uid=" + uid + "&func=process&type=" + type + "&cmd=" + cmds ;
            string responseString = client.GetStringAsync(urlreq).Result;
            return (responseString);
        }

        public string[] getSharedGrammars()
        {
            string urlreq = urlapi + "uid=" + uid + "&func=listinclude";
            string responseString = client.GetStringAsync(urlreq).Result;
            string[] grammarFiles = responseString.Split(';');
            return (grammarFiles);
        }

        public string[] getGrammars()
        {
            string urlreq = urlapi + "uid=" + uid + "&func=listgrammar";
            string responseString = client.GetStringAsync(urlreq).Result;
            string[] grammarFiles = responseString.Split(';');
            return (grammarFiles);
        }

        public string[] getsubGrammars()
        {
            string urlreq = urlapi + "uid=" + uid + "&func=listsubgrammar";
            string responseString = client.GetStringAsync(urlreq).Result;
            string[] grammarFiles = responseString.Split(';');
            return (grammarFiles);
        }

        public string getGrammarFile(string filename)
        {
            string urlreq = urlapi + "uid=" + uid + "&func=getGrammar&file="+ filename;
            string responseXML = client.GetStringAsync(urlreq).Result;
            
            return (responseXML);
        }

        public string getConfig()
        {
            string urlreq = urlapi + "uid=" + uid + "&func=getvoiceconfig";
            string responseString = "ERROR";
            try
            { 
                responseString = client.GetStringAsync(urlreq).Result;
            }
            catch(Exception e)
            {
                Console.WriteLine("Unable to connect to server : "+e.Message);
                responseString = "ERROR";
            }
            return (responseString);
        }

    }
}
