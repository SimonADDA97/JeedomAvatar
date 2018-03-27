using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Speech.Synthesis;

namespace JDSpeech
{
    class Speaker
    {
        SpeechSynthesizer synthesizer;
        public string voicename;

        public Speaker()
        {
            synthesizer = new SpeechSynthesizer();
        }

        public void Dispose()
        {
            synthesizer.Dispose();
        }
        public void Initialize()
        {
            synthesizer.SelectVoice(voicename);
        }

        public List<string> getVoices()
        {
            List<string> returntxt = new List<string>();
            
            System.Collections.ObjectModel.ReadOnlyCollection<InstalledVoice> voices = synthesizer.GetInstalledVoices();

            foreach (InstalledVoice voice in voices)
            {
                returntxt.Add(voice.VoiceInfo.Name);
            }

            return (returntxt);
        }


        public void setVoice(string newvoicename)
        {
            voicename = newvoicename;
            synthesizer.SelectVoice(voicename);
        }

        public void Speak( string texttoo )
        {
            synthesizer.Speak(texttoo);
        }
            
    }
}
