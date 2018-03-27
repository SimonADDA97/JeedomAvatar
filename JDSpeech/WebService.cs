using System;
using System.ServiceModel;
using System.ServiceModel.Web;
using Newtonsoft.Json;

namespace JDSpeech
{
    
    [ServiceContract]
    public interface IRestService
    {

        [OperationContract]
        [WebGet(UriTemplate = "test", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string Test();

        // speech

        [OperationContract]
        [WebGet(UriTemplate = "reloadgrammar", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string ReLoadGrammars();

        [OperationContract]
        [WebGet(UriTemplate = "disablegrammar/{grammarname}", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string DisableGrammar(string grammarname);
        

        [OperationContract]
        [WebGet(UriTemplate = "stopspeech", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool StopSpeech();

        [OperationContract]
        [WebGet(UriTemplate = "startspeech", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool StartSpeech();

        [OperationContract]
        [WebGet(UriTemplate = "pausespeech", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool PauseSpeech();

        [OperationContract]
        [WebGet(UriTemplate = "playspeech", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool PlaySpeech();

        // http://localhost:8244/startspeech

        // config

        [OperationContract]
        [WebGet(UriTemplate = "reloadconfig", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string ReLoadConfig();

        // test
        // http://localhost:8244/get/d9f833e6-cae6-4528-8c17-961df65ddf5f/70180110163999

        // voice

        [OperationContract]
        [WebGet(UriTemplate = "stopvoice", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool StopVoice();

        [OperationContract]
        [WebGet(UriTemplate = "startvoice", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        bool StartVoice();

        [OperationContract]
        [WebGet(UriTemplate = "say/{msg}", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string Say(string msg);

        [OperationContract]
        [WebGet(UriTemplate = "voices", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string getVoices();
        // http://localhost:8244/voices

        [OperationContract]
        [WebGet(UriTemplate = "setvoice={newvoice}", RequestFormat = WebMessageFormat.Json, ResponseFormat = WebMessageFormat.Json)]
        string setVoice(string newvoice);
        // http://localhost:8244/setvoice=LH Pierre

    }

    [ServiceBehavior(InstanceContextMode = InstanceContextMode.Single)]
    public class api : IRestService
    {
        // context

        Speaker mySpeaker;
        Config myConfig;
        SpeechReco recotask;
        bool voicenabled;
        bool recoenabled;

        public api()
        {
            myConfig = new Config();
            myConfig.Load();

            voicenabled = myConfig.getbool("voiceenabled");
            if (voicenabled)
                voicenabled = StartVoice();

            recoenabled = myConfig.getbool("recoenabled");
            if (recoenabled)
                recoenabled = StartSpeech();
            
        }

        public string Test()
        {
            return ("test ok");
        }

        public bool StartVoice()
        {
            Console.WriteLine("Voice start... ");

            try
            {
                mySpeaker = new Speaker();
                mySpeaker.voicename = myConfig.getstring("Voice", "");
                mySpeaker.Initialize();
            }
            catch ( Exception mye)
            {
                Console.WriteLine("ERROR");
                Console.WriteLine(mye.Message);
                return (false);
            }

            Console.WriteLine("OK");
            return (true);
        }

        public bool StopVoice()
        {
            Console.WriteLine("Voice stop");

            try
            {
                mySpeaker.Dispose();
            }
            catch (Exception mye)
            {
                Console.WriteLine("ERROR");
                Console.WriteLine(mye.Message);
            }

            return (false);
        }

        public bool StopSpeech()
        {
            recotask.Dispose();
            return (false);
        }

        public bool PauseSpeech()
        {
            recotask.Pause();
            return (true);
        }

        public bool PlaySpeech()
        {
            recotask.Play();
            return (true);
        }

        public bool StartSpeech()
        {
            Console.WriteLine("Speech recognition start... ");

            try
            {
                recotask = new SpeechReco();
                if (voicenabled)
                { 
                    recotask.Speaker = mySpeaker;
                    recotask.voicenabled = true;
                }
            }
            catch (Exception mye)
            {
                Console.WriteLine("ERROR");
                Console.WriteLine(mye.Message);
                return (false);
            }

            Console.WriteLine("OK");
            return (true);
        }

        public string ReLoadGrammars()
        {
            recotask.ReLoadGrammars();
            return ("reload ok");
        }

        public string DisableGrammar(string grammarname)
        {
            recotask.DisableGrammar(grammarname);
            return ("disable ok");
        }

        public string ReLoadConfig()
        {
            bool memovoice = voicenabled;
            bool memoreco = recoenabled;

            myConfig.Load();

            voicenabled = myConfig.getbool("voiceenabled");
            if (voicenabled & !memovoice)
                voicenabled = StartVoice();
            else if (!voicenabled & memovoice)
                voicenabled = StopVoice();

            recoenabled = myConfig.getbool("recoenabled");
            if (recoenabled & !memoreco)
                recoenabled = StartSpeech();
            else if (!recoenabled & memoreco)
                recoenabled = StopSpeech();
            else if (memoreco)
                recotask.LoadConfig();

            return ("reload ok");
        }


        public string Say(string msg)
        {
            mySpeaker.Speak(msg);
            return ("ok");
        }

        public string getVoices()
        {
            
            return JsonConvert.SerializeObject(mySpeaker.getVoices(), Formatting.Indented);

        }


        public string setVoice(string newvoice)
        {
            mySpeaker.setVoice(newvoice);
            myConfig.setstring("Voice",newvoice);
            myConfig.save();
            return ("Voice set to " + newvoice);
        }

    }
}
