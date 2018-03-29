using System;
using System.IO;
//using System.Speech.Recognition;
using System.Speech.Recognition;
using System.Speech.Recognition.SrgsGrammar;

namespace JDSpeech
{
    class SpeechReco
    {
        SpeechRecognitionEngine recognizer;

        Jeedom jeedomClient;
        
        public Speaker mySpeaker;
        public bool voicenabled=false;

        public int confidence=70;
        public bool debugmode=false;
        public string jeedomURL="";
        public string jeedomUID="";
        public string room="";
        public string roomd="";
        public string level="";
        public string leveld="";
        public string levela="";
        public string cultureinfo = "";
        public bool subgrammarenabled=false;


        public SpeechReco()
        {
            LoadConfig();

            jeedomClient = new Jeedom(jeedomURL, jeedomUID);
            
        }

        public void start()
        {
            try
            {
                recognizer = new SpeechRecognitionEngine(new System.Globalization.CultureInfo(cultureinfo));
            }
            catch (Exception e)
            {
                Console.WriteLine("Error starting recognition engine : " + e.Message);
                return;
            }

            // load grammar
            LoadGrammars();

            // Attach event handlers for recognition events.
            recognizer.SpeechRecognized +=
              new EventHandler<SpeechRecognizedEventArgs>(
                SpeechRecognizedHandler);

            recognizer.SetInputToDefaultAudioDevice(); // set the input of the speech recognizer to the default audio device
            recognizer.RecognizeAsync(RecognizeMode.Multiple); // recognize speech asynchronous

        }

        public void LoadConfig()
        {
            Config myConfig = new Config();
            myConfig.Load();

            cultureinfo = myConfig.getstring("CultureInfo", "fr - FR");
            jeedomURL = myConfig.getstring("JeedomURL", "");
            jeedomUID = myConfig.getstring("JeedomUID", "");
            room = myConfig.getstring("Room", "");
            roomd = myConfig.getstring("Roomd", "");
            level = myConfig.getstring("Level", "");
            leveld = myConfig.getstring("Leveld", "");
            levela = myConfig.getstring("Levela", "");
            confidence = myConfig.getint("Confidence");
            debugmode = myConfig.getbool("DebugMode");
            myConfig.Dispose();

        }

        public void Pause()
        {
            recognizer.RecognizeAsyncCancel();
            Console.WriteLine("Recognition engine Paused.");

        }

        public void Play()
        {
            recognizer.RecognizeAsync(RecognizeMode.Multiple);
            Console.WriteLine("Recognition engine retarted.");
        }

        public void Dispose()
        {
            Console.WriteLine("Recognition engine stopped.");
            mySpeaker.Dispose();
            recognizer.Dispose();
        }

        public void ReLoadGrammars()
        {
            recognizer.UnloadAllGrammars();
            LoadGrammars();
        }

        public void LoadGrammars()
        {
            // load all grammar.

            Grammar testGrammar;
            SrgsDocument sgrsDoc;
            string stringgrammar = "";
            subgrammarenabled = false;
            Console.WriteLine("Load Active grammar :");

            string[] grammarFiles = jeedomClient.getGrammars();
            foreach (string grammarFile in grammarFiles)
            {
                if (grammarFile != "")
                { 
                    stringgrammar = jeedomClient.getGrammarFile(grammarFile);
                    sgrsDoc = new SrgsDocument(System.Xml.XmlReader.Create(new StringReader(stringgrammar)));

                    testGrammar = new Grammar(sgrsDoc);
                    testGrammar.Name = "GR " + grammarFile;
                    testGrammar.Enabled = true;

                    recognizer.LoadGrammar(testGrammar);
                    Console.WriteLine(" - {0}", testGrammar.Name);
                }
            }

            Console.WriteLine("Load Inactive grammar :");

            string[] subgrammarFiles = jeedomClient.getsubGrammars();
            foreach (string grammarFile in subgrammarFiles)
            {
                if (grammarFile != "")
                {
                    stringgrammar = jeedomClient.getGrammarFile(grammarFile);
                    sgrsDoc = new SrgsDocument(System.Xml.XmlReader.Create(new StringReader(stringgrammar)));

                    testGrammar = new Grammar(sgrsDoc);
                    testGrammar.Name = "SUB " + grammarFile;
                    testGrammar.Enabled = false;

                    recognizer.LoadGrammar(testGrammar);
                    Console.WriteLine(" - {0}", testGrammar.Name);
                }
            }
        }


        public void EnableGrammar(string grammarname)
        {
            foreach (var grammar in recognizer.Grammars)
            {
                if (grammar.Name == grammarname)
                    grammar.Enabled = true;
            }
        }

        public void DisableGrammar(string grammarname)
        {
            foreach (var grammar in recognizer.Grammars)
            {
                if (grammar.Name == grammarname)
                    grammar.Enabled = false;
            }
        }

        public void DisableSubGrammars()
        {
            foreach (var grammar in recognizer.Grammars)
            {
                if (grammar.Name.Substring(0, 3) == "SUB")
                    grammar.Enabled = false;
            }
        }

        public void ProcessMatch(int match, string phrase, RecognitionResult rre )
        {
            string rr_command = "";
            string rr_type = "";
            string rr_reply = "";
            string rr_say = "";
            string rr_chain = "";
            string rr_infos = "";
            string rr_noanswer = "";

            if (match >= confidence)
            { 
                Console.WriteLine("Recognition result = {0}  {1}", phrase ?? "<no text>", match);

                if (subgrammarenabled)
                {
                    // stop timer here

                    if (rre.Grammar.Name.Substring(0, 2) == "GR")
                        DisableSubGrammars();
                    else
                        rre.Grammar.Enabled = false;
                }

                foreach (var semant in rre.Semantics)
                {
                    if (semant.Key == "command")
                        rr_command = semant.Value.Value.ToString();
                    else if (semant.Key == "type")
                        rr_type = semant.Value.Value.ToString();
                    else if (semant.Key == "reply")
                        rr_reply = semant.Value.Value.ToString();
                    else if (semant.Key == "say")
                        rr_say = semant.Value.Value.ToString();
                    else if (semant.Key == "chain")
                        rr_chain = semant.Value.Value.ToString();
                    else if (semant.Key == "noanswer")
                        rr_noanswer = semant.Value.Value.ToString();
                }

                rr_command = AddKeyWords(rr_command, "");


                if (debugmode)
                {
                    Console.WriteLine("   command  : {0}", rr_command);
                    Console.WriteLine("   type     : {0}", rr_type);
                    rr_reply = AddKeyWords(rr_reply, "");
                    Console.WriteLine("   reply    : {0}", rr_reply);
                    rr_say = AddKeyWords(rr_say, "");
                    Console.WriteLine("   say      : {0}", rr_say);
                    return;
                }

                if (rr_say != "")
                {
                    rr_say=AddKeyWords(rr_say, "");
                    if (voicenabled)
                        mySpeaker.Speak(rr_say);
                    else
                        Console.WriteLine(" Voice disabled . Can't say   : {0}", rr_say);
                }

                if (rr_type == "jeedom_action" )
                    rr_infos = jeedomClient.send(rr_command, "action");

                if (rr_type == "jeedom_info")
                    rr_infos = jeedomClient.send(rr_command, "info");

                if ( Neededinfos(rr_infos,rr_reply) & (rr_reply != "") )
                {
                    rr_reply = AddKeyWords(rr_reply, rr_infos);
                    
                    if (voicenabled)
                        mySpeaker.Speak(rr_reply);
                    else
                        Console.WriteLine(" Voice disabled . Can't reply   : {0}", rr_reply);
                }

                if ( rr_noanswer!="" & rr_infos=="" )
                {
                    rr_noanswer = AddKeyWords(rr_noanswer, "");

                    if (voicenabled)
                        mySpeaker.Speak(rr_noanswer);
                    else
                        Console.WriteLine(" Voice disabled . Can't reply   : {0}", rr_noanswer);


                }

                if (rr_chain != "")
                { 
                    EnableGrammar(rr_chain);
                    // launch timer 
                }

            }
        }

        public bool Neededinfos(string infosreceived , string replyphrase)
        {

            if (( replyphrase == "") | ((replyphrase.IndexOf("{info", 0) > 0 ) & infosreceived =="") )
                    return false;

            return true;
        }

        public string AddKeyWords(string phrase,string infos)
        {
            string phrasecomplete = phrase;

            /* Replace keywords
                {info}  par valeur retournée
                {level}  par   "rez de chaussé" ou "étage"
                {leveld}  par   "du rez de chaussé" ou "de l'étage"
                {levela}  par   "au rez de chaussé" ou "a l'étage"
                {room}  par  le nom de la piece.
                {roomd}  par  le nom de la piece précédé de : du / de / de la
            */
            string[] infoArray = infos.Split(';');
            int inc = 1;
            foreach (string info in infoArray)
            { 
                phrasecomplete = phrasecomplete.Replace("{info"+inc+"}", info);
                inc++;
            }

            phrasecomplete = phrasecomplete.Replace("{level}", level);
            phrasecomplete = phrasecomplete.Replace("{levela}", levela);
            phrasecomplete = phrasecomplete.Replace("{leveld}", leveld);
            phrasecomplete = phrasecomplete.Replace("{room}", room);
            phrasecomplete = phrasecomplete.Replace("{roomd}", roomd);


            return (phrasecomplete);
        }


        // Handle the SpeechRecognized event.
        void SpeechRecognizedHandler(
          object sender, SpeechRecognizedEventArgs e)
        {
            if (e.Result != null)
            {
                int match = (int)(e.Result.Confidence * 100);
                ProcessMatch(match, e.Result.Text, e.Result);
            }

        }

    }
}
