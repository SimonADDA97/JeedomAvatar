using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.ServiceModel.Web;
using System.ServiceProcess;
using System.Text;
using System.Threading.Tasks;

namespace JDSpeech
{
    static class Program
    {
        /// <summary>
        /// Point d'entrée principal de l'application.
        /// </summary>
       
        
        static void Main()
        {
            Config myConfig = new Config();
            WebServiceHost serviceHost;
            // load online config and save
            if (myConfig.Load())
            {


                //string defaultip = LocalIPAddress().ToString();
                string defaultip = myConfig.getstring("ServerIP", "127.0.0.1");
                string defaultport = myConfig.getstring("ETHPort", "8244");
                myConfig.Dispose();

                Console.WriteLine("Webservice started on http://" + defaultip + ":" + defaultport);

                serviceHost = new WebServiceHost(typeof(api), new Uri("http://" + defaultip + ":" + defaultport));
                try
                { 
                
                serviceHost.Open();
                }
                catch (Exception mye)
                {
                    Console.WriteLine("Error starting webservice "+mye.Message);

                }

                //Console.WriteLine("to start voice recognition http://" + defaultip + ":8244/startspeech");
                Console.WriteLine("Hit Q to quit...");
                while (Console.ReadKey().Key != ConsoleKey.Q) { }
                serviceHost.Close();

            }
            else
            { 
                Console.WriteLine("Unable to start webservice");
                Console.WriteLine("Press any key ...");
                Console.ReadKey();
            }

        }

    }
}
