# Music Production Manager

Music Production Management is a software used in music production. Music Production Management is not a DAW or Digital Audio Workstation although it is equipped with features such as changing instruments and creating lyrics in MIDI files.

MIDI files are one of the focuses in **Music Production Manager**. MIDI files can be used to create sheet music that can be printed in a variety of formats. MIDI files can also be converted into MusicXML format.

Not all DAWs support the MusicXML format but many DAWs support the MIDI format.

## Features

1. Colaboration
2. Song Management
3. Album Management
4. Artist Management
5. genre management
6. Genre Management
7. Song Attachment
8. Comment
9. Lyric Editor
10. MIDI Editor
11. MIDI Player
12. MusicXML Player
13. Karaoke
14. Sheet Music
15. Recording Teleprompter
16. Piano Display
17. Download Files
18. User Management
19. User Role
20. Article

## Colaboration

**Music Production Manager** is used jointly by several artists involved in music production, both as composer, arranger, vocalist and even producer. Music that has been created by the arranger and given lyrics by the composer can be reviewed by other artists and then given a rating. A low rating indicates that the song still has many shortcomings. Apart from that, other artists can also provide comments on parts of the song that need improvement.

Both the composer and arranger can improve the song and then it can be reviewed again by another artist.

Vocalists can learn the song, both the lyrics and the tune. Vocalists can read musical notes and see the piano keys while the song is being played to avoid note mistakes when singing it.

## Song Management

**Music Production Manager** arranges songs during the production process. Songs can be arranged in an album, extended player, single player or group of songs. Songs can be sorted by track number or rating. Rating is very important during song composition. A song with a low rating means there are still many shortcomings that need to be corrected. Producers can focus on improving songs with lower rating.

Users can search for songs by typing keywords contained in the song subtitles. The keywords chosen should be unique and rarely used so they will provide more accurate results. **Music Production Manager** will display several songs with subtitles containing typed words.

## Album Management

**Music Production Manager** manages the song groups. Groups are very important because the release of an album or extended player does not depend on when the song was created. Apart from that, in the creation process, the song does not yet have a final title. The producer may give each song a name with a certain code, for example a series code.

Song groups marked as draft cannot yet be seen from other modules but can still be seen in modules related to editing. The same also applies to the content of the song group.

## Artist Management

The Music Production Manager manages the artists involved in the creation of each song. The artist can have roles as vocalist, composer or arranger. Songs can be sorted by artist from the above roles.

## Genre Management

Music Production Management can set the genre of each song. A song will have one genre.

All albums can consist of several genres.

## Song Attachment

Producers can attach multiple documents or files to each song.

## Comment

Users can provide comments on each song. Comments have time and duration like subtitles. Comments can be seen by other users.

Comments will be notes by the composer, arranger, vocalist or producer about parts of a song.

## Lyric Editor

When creating a song, perhaps the instruments and lyrics are created simultaneously. The lyric editor will really help both composers and arrangers in synchronizing instruments and lyrics.

The lyric editor is also used to synchronize the time of the lyric. Lyrics can be exported into SRT format which is compatible with a variety of player software.

When taking vocals, the lyrics can help the vocalist, making it easier to sing a song.

Lyrics in SRT format will be displayed in the form of phrases or sentences. Generally, there are breaks between lines of lyrics. Usually these lyrics are displayed ahead of time to give the vocalist enough time to read the lyric lines.

## MIDI Editor

### Replace MIDI Instrument

**Music Production Manager** has the ability to change instruments in MIDI files.

### Add and Update Lyric

MIDI lyrics are based on syllables and have the exact time the syllables are sung. MIDI lyrics will be saved to a MIDI file and can be converted into sheet music. Sheet music can be sung by vocalists either verbally or using melodic instruments such as piano, saxophone, trumpet, guitar, etc.

### Rescale MIDI

**Music Production Manager** can change the MIDi scale, namely by changing the tempo and time base with a ratio of 2^x where x is an integer number, either positive or negative like (..., 1/16, 1/8, 1/4, 1/2, 1, 2, 4, 8, 16, ...).

This feature is very useful for repairing MIDI files so that the bar size is as desired.

As an example:

A song is made with a tempo of 130 bpm but in fact, the song is only 65 bpm. The song has no problems when played in either mp3 or MIDI format. However, when creating musical notes or number notes from MIDi format, the musical note reader will have difficulty because the note is too high. **Music Production Manager** can fix this problem very easily by converting it to 1/2 scale. There is no change in the song when played with a MIDI player application even though the tempo is reduced by half.

## Sheet Music

**Music Production Manager** supports scores that can be printed on paper. This score is needed by vocalists, musicians and even arrangers to understand the song written by the composer correctly. Just listening to the draft music made by the composer may cause errors in understanding both pitch, beat and tempo. The sheet music is the main reference that must be adhered to by all parties.

Apart from sheet music, the Music Production Manager also provides plain lyrics for vocalists to understand the meaning of the song so that it can evoke emotions when singing it. These lyrics can be printed on a separate sheet but are still in the same PDF file as the sheet music.

### Transpose MIDI

After the instruments are arranged and the lyrics are finished writing, the song still has to adapt to the vocalist. If the note is too low or too high compared to the vocalist's ability to sing the song, it is necessary to transpose the note. Transpose is raising or lowering the note by half a note.

Users can choose whether to apply transpose to all trucks, all channels, or only to certain buses and channels. However, **Music Production Manager** will skip channel 10 in the transpose process because that channel is designated as the channel for percussion instruments. UI allso prevent user select chanell 10.

## MIDI Player

**Music Production Manager** is equipped with a MIDI player that can be used when editing MIDI lyrics.

## MusicXML Player

**Music Production Manager** is equipped with a MusicXML player which can be used both when the vocalist is practicing and when taking vocals. MusicXML player will display the music rating in sync with the music being played.

MIDI editor is useful for changing instruments on each song track. Producers may need to change the instruments of songs to MIDI format that have been uploaded to **Music Production Manager** without having to download the MIDI file and edit it using a third-party application.

## Karaoke

Karaoke is a feature that vocalists can use while practicing. Features can be accessed in both desktop and mobile mode using smartphones and tablets.

## Recording Teleprompter

Teleprompter is used when taking vocals. The teleprompter will display the lyrics on the vocalist's screen during the vocal take. The lyrics will be in sync with the music listened to through the vocalist's monitor headphones.

## Piano Display

Apart from displaying the lyric text, **Music Production Manager** also displays the piano keys when the song is sung according to the note that must be sung by the vocalist. This really helps the vocalist to sing the correct notes instead of relying on the ear to hear the notes according to the pitch of the lead instrument which may not be heard clearly because it is mixed with other instruments at a higher level.

This feature is important to prevent vocalists from singing songs incorrectly.

## Download Files

Users can download files individually, download files per song, or download files per album. The types of files that can be downloaded are as follows:

1. MP3 (audio files)
2. MIDI (audio files)
3. MusicXML (xml files)
4. PDF (music score file)
5. SRT (subtitle for YouTube and other supported applications)

When downloading files per song or per album, **Music Production Manager** will only download files that have been uploaded by the user.

## User Management

Like most applications, **Music Production Manager** provides user management so that it can be used by many users. A user can add other users, change data, deactivate, block and delete other users' data.

## User Role

Users can be given access rights according to their role in the application or music production.

User roles include the following:

1. administrators
2. producer
3. composer
4. arranger
5. vocalist

Users with the administrator role have more access rights than others.

Users with the role of arranger can edit instruments in MIDI.

Users with the composer role can edit lyrics and subtitles on MP3 and MIDI files.

Users with the role of vocalist cannot edit lyrics and subtitles on MP3 or MIDI files.

## Article

**Music Production Studio** provides space for articles that can be read by all users. This article may contain:

1. music theory
2. music production application tutorial
3. tips and tricks for making songs

Any user can publish articles. This article may be modified by other users. Each change will be saved in a log so that changes can be tracked.

# Programming Language

**Music Production Manager** use PHP

# Library

**Music Production Manager** use **MagicObject** (https://github.com/Planetbiru/MagicObject)

# Database

**Music Production Manager** use MariaDB or MySQL database.

# System Rquirement

1. Apache Web Server
2. PHP runtime
3. MariaDB or MySQL Database

# Installation

To install **Music Production Manager**, clone **MusicProductionManager** repository from Github.

## Clone Repository

```git
git clone https://github.com/kamshory/MusicProductionManager.git
```

## Create Configuration

User must setup application configuration.

```yaml
result_per_page: 20
song_base_url: ${SONG_BASE_UER}
app_name: Music Production Manager
user_image:
  width: 512
  height: 512
album_image:
  width: 512
  height: 512
song_image:
  width: 512
  height: 512
database:
  time_zone_system: Asia/Jakarta
  default_charset: utf8
  driver: ${APP_DATABASE_TYPE}
  host: ${APP_DATABASE_SERVER}
  port: ${APP_DATABASE_PORT}
  username: ${APP_DATABASE_USER}
  password: ${APP_DATABASE_PASSWORD}
  database_name: ${APP_DATABASE_NAME}
  database_schema: public
  time_zone: ${APP_DATABASE_TIME_ZONE}
  salt: ${APP_DATABASE_SALT}
```

## Environment Variable

On Windows, users can directly create environment variables either via the graphical user interface (GUI) or the `setx` command line. PHP can immediately read environment variables after Windows is restarted.

On Linux, users must create a configuration on the Apache server by creating a file with the .conf extension in the `/etc/httpd/conf.d` folder then restart Apache web server.

**Windows**

Setup environtment variable on Windows using command lines.

```bash
SETX SONG_BASE_UER "https://domain.tld/path"
SETX APP_DATABASE_TYPE "mariadb"
SETX APP_DATABASE_SERVER "localhost"
SETX APP_DATABASE_PORT "3306"
SETX APP_DATABASE_USER "user"
SETX APP_DATABASE_PASSWORD "pass"
SETX APP_DATABASE_NAME "music"
SETX APP_DATABASE_TIME_ZONE "Asia/Jakarta"
SETX APP_DATABASE_SALT "GaramDapur"
```

**Linux**

Setup environtment variable on Linux using command lines create new file configuration used by Apache web server and consumed by PHP.

```bash
echo -e '' > /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv SONG_BASE_UER "https://domain.tld/path"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_TYPE "mariadb"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_SERVER "localhost"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_PORT "3306"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_USER "user"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_PASSWORD "pass"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_NAME "music"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_TIME_ZONE "Asia/Jakarta"' >> /etc/httpd/conf.d/mpm.conf
echo -e 'SetEnv APP_DATABASE_SALT "GaramDapur"' >> /etc/httpd/conf.d/mpm.conf

service httpd restart
```

## Create New User Account

User must create an user account on installation. Without this, user can not create account.

# For Developer

```
composer --with-all-dependencies require setasign/fpdi-fpdf
```

# Need Support

Please support or YouTube channel https://www.youtube.com/@maliktamvan
