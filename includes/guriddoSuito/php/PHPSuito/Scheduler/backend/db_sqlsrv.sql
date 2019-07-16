-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE [dbo].[events](
    [event_id] [int] IDENTITY(1,1) NOT NULL,
    [user_id] [int] NOT NULL DEFAULT 0,
    [start_event] [int] NOT NULL DEFAULT 0,
    [end_event] [int] NOT NULL DEFAULT 0,
    [title] [varchar](255) NOT NULL,
    [description] [text] NOT NULL,
    [location] [varchar](255) NOT NULL DEFAULT '',
    [categories] [varchar](255) NOT NULL DEFAULT '',
    [access] [varchar](255) NOT NULL DEFAULT '',
    [all_day] [tinyint] NOT NULL DEFAULT 0,
PRIMARY KEY CLUSTERED
(
    [event_id] ASC
))