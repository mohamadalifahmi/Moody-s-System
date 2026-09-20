; Inno Setup script for Moody's Management Desktop App
#define MyAppName "Moody's Management"
#define MyAppVersion "1.0.0"
#define MyAppPublisher "Moody's"
#define MyAppExeName "start.bat"
#define SourceDir "build\MoodysApp"

[Setup]
AppId={{8E0C3F1A-2D4A-4B6E-9F0C-MOODYSAPP}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
DefaultDirName={localappdata}\{#MyAppName}
DefaultGroupName={#MyAppName}
DisableProgramGroupPage=yes
OutputDir=build
OutputBaseFilename=Setup-MoodysApp
Compression=lzma2/max
SolidCompression=yes
WizardStyle=modern
SetupLogging=yes
ArchitecturesInstallIn64BitMode=x64compatible
PrivilegesRequired=lowest
PrivilegesRequiredOverridesAllowed=dialog
SetupIconFile=build\MoodysApp\app-icon.ico
UninstallDisplayIcon={app}\app-icon.ico
CloseApplications=no
AppMutex=MoodysApp

[Languages]
Name: "arabic"; MessagesFile: "compiler:Languages\Arabic.isl"
Name: "english"; MessagesFile: "compiler:Default.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked
Name: "autostart"; Description: "Launch {#MyAppName} after installation"; GroupDescription: "{cm:AdditionalIcons}"

[Files]
Source: "{#SourceDir}\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs

[Icons]
Name: "{group}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; IconFilename: "{app}\app-icon.ico"; WorkingDir: "{app}"
Name: "{group}\Stop Server"; Filename: "{app}\stop.bat"; IconFilename: "{app}\app-icon.ico"; WorkingDir: "{app}"
Name: "{group}\{cm:UninstallProgram,{#MyAppName}}"; Filename: "{uninstallexe}"
Name: "{autodesktop}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; IconFilename: "{app}\app-icon.ico"; WorkingDir: "{app}"; Tasks: desktopicon

[Run]
Filename: "{app}\{#MyAppExeName}"; Description: "{cm:LaunchProgram,{#MyAppName}}"; WorkingDir: "{app}"; Flags: nowait postinstall skipifsilent; Tasks: autostart

[UninstallDelete]
Type: filesandordirs; Name: "{app}"