# Music Production Manager

Music Production Management is a software used in music production. Music Production Management is not a DAW or Digital Audio Workstation although it is equipped with features such as changing instruments and creating lyrics in MIDI files.

## Feature

1. song management
2. album management
3. artist management
4. genre management
5. song attachment
6. comment
7. lyric editor
8. MIDI editor
9. karaoke
10. recording teleprompter

## Song Management

**Music Production Manager** arranges songs during the production process. Songs can be arranged in an album, extended player, single player or group of songs. Songs can be sorted by track number or score. Score is very important during song composition. A song with a low score means there are still many shortcomings that need to be corrected. Producers can focus on improving songs with lower scores.

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

Lyric editors are divided into 2, namely SRT and MIDI.

### SRT

Lyrics in SRT format will be displayed in the form of phrases or sentences. Generally, there are breaks between lines of lyrics. Usually these lyrics are displayed ahead of time to give the vocalist enough time to read the lyric lines.

### MIDI

MIDI lyrics are based on syllables and have the exact time the syllables are sung. MIDI lyrics will be saved to a MIDI file and can be converted into sheet music. Sheet music can be sung by vocalists either verbally or using melodic instruments such as piano, saxophone, trumpet, guitar, etc.

## MIDI Editor

MIDI editor is useful for changing instruments on each song track. Producers may need to change the instruments of songs to MIDI format that have been uploaded to **Music Production Manager** without having to download the MIDI file and edit it using a third-party application.

## Karaoke

Karaoke is a feature that vocalists can use while practicing. Features can be accessed in both desktop and mobile mode using smartphones and tablets.

## Recording Teleprompter

Teleprompter is used when taking vocals. The teleprompter will display the lyrics on the vocalist's screen during the vocal take. The lyrics will be in sync with the music listened to through the vocalist's monitor headphones.

## Download Files

Users can download files individually, download files per song, or download files per album. The types of files that can be downloaded are as follows:

1. MP3 (audio files)
2. MIDI (audio files)
3. XML (audio files)
4. PDF (music score file)

When downloading files per song or per album, **Music Production Manager** will only download files that have been uploaded by the user.

# Language

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

**Clone Repository**

```git
git clone https://github.com/kamshory/MusicProductionManager.git
```

**Create Configuration**

User must setup application configuration.

**Create New User Account**

User must create an user account.
