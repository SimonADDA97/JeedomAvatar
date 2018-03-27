using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;


namespace JDSpeech
{
    public class Config
    {
        public string rootPath;
        public DataTable DBSettings;
        public string errormsg;

        public void Dispose()
        {
            DBSettings.Dispose();
        }


        public void setint(string Name, int Value)
        {
            DataRow[] rows = DBSettings.Select("Name ='" + Name + "'");

            if (rows.Length > 0)
                rows[0]["Value"] = Value.ToString();
            else
            {
                DataRow newrow = DBSettings.NewRow();
                newrow["Name"] = Name;
                newrow["Value"] = Value.ToString();
                DBSettings.Rows.Add(newrow);
            }
        }

        public int getint(string Name)
        {
            string Svalue = "0";

            DataRow[] rows = DBSettings.Select("Name ='" + Name + "'");
            if (rows.Length > 0)
                Svalue = rows[0]["Value"].ToString();

            return (Int32.Parse(Svalue));
        }


        public void setbyte(string Name, byte Value)
        {
            DataRow[] rows = DBSettings.Select("Name ='" + Name + "'");

            if (rows.Length > 0)
                rows[0]["Value"] = Value.ToString();
            else
            {
                DataRow newrow = DBSettings.NewRow();
                newrow["Name"] = Name;
                newrow["Value"] = Value.ToString();
                DBSettings.Rows.Add(newrow);
            }
        }

        public byte getbyte(string Name)
        {
            string Svalue = "0";

            DataRow[] rows = DBSettings.Select("Name ='" + Name + "'");
            if (rows.Length > 0)
                Svalue = rows[0]["Value"].ToString();

            return (Byte.Parse(Svalue));
        }

        public void setstring(string Name, string Value)
        {

            try
            {
                DataRow[]  rows = DBSettings.Select("Name ='" + Name + "'");
                rows[0]["Value"] = Value;
            }
            catch(Exception e)
            {
                DataRow newrow = DBSettings.NewRow();
                newrow["Name"] = Name;
                newrow["Value"] = Value;
                DBSettings.Rows.Add(newrow);

            }

        }

        public string getstring(string Name,string defaultstring)
        {
            string Value = defaultstring;

            try
            {
                DataRow[] rows = DBSettings.Select("Name ='" + Name + "'");
                Value = rows[0]["Value"].ToString();
            }
            catch (Exception e)
            {
            }

            return (Value);
        }

        public void setbool(string Name, bool Value)
        {
            if (Value)
                setstring(Name, "1");
            else
                setstring(Name, "0");
        }

        public bool getbool(string Name)
        {
            string Value = getstring(Name, "0");

            if ( Value=="1" )
                return (true);
            else
                return (false);
        }

        public void restoreSettings()
        {

            DataTable table = new DataTable();
            table.TableName = "Settings";
            table.Columns.Add("Name", typeof(string));
            table.Columns.Add("Value", typeof(string));

            DataRow row = table.NewRow();
            row["Name"] = "Dummy";
            row["Value"] = "TEST";
            table.Rows.Add(row);

            table.WriteXml(@Environment.CurrentDirectory + "\\Config\\Settings.xml", System.Data.XmlWriteMode.WriteSchema);
        }

        public bool Loadlocal()
        {
            rootPath = @Environment.CurrentDirectory;

            if (!File.Exists(rootPath + "\\Config\\Settings.xml"))
                restoreSettings();

            // Load Settings
            DBSettings = new DataTable();
            try
            {
                DBSettings.ReadXml(rootPath + "\\Config\\Settings.xml");
            }
            catch (Exception e)
            {
                return false;
            }

            return true;
        }


        public bool Load()
        {
            Loadlocal();
           
            Console.Write("Connect server to get config.");
            string jeedomURL = getstring("JeedomURL", "");
            string jeedomUID = getstring("JeedomUID", "");
            Jeedom jeedomClient = new Jeedom(jeedomURL, jeedomUID);

            string olconfig = jeedomClient.getConfig();

            if (olconfig == "ERROR")
                return false;

            string[] aconf = olconfig.Split(';');

            foreach (string iconf in aconf)
            {
                string[] conf = iconf.Split('=');
                setstring(conf[0], conf[1]);
            }

            jeedomClient.Dispose();
            return true;
        }
        

        public void save()
        {
            // Save Settings
            DBSettings.WriteXml(rootPath + "\\Config\\Settings.xml", System.Data.XmlWriteMode.WriteSchema);
        }
        

    }
}
