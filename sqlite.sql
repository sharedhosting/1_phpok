-- SQLite3 Database Structure for qgweb system

-- Table: sino_ad
CREATE TABLE sino_ad (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    typeid INTEGER NOT NULL DEFAULT 0,
    subject TEXT NOT NULL,
    content TEXT NOT NULL,
    status INTEGER NOT NULL DEFAULT 0,
    start_date TEXT NOT NULL DEFAULT '2008-02-29',
    end_date TEXT NOT NULL DEFAULT '2020-02-29'
);

-- Table: sino_admin
CREATE TABLE sino_admin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    typer TEXT NOT NULL DEFAULT 'editor' CHECK(typer IN ('system', 'manager', 'editor')),
    user TEXT NOT NULL DEFAULT '',
    pass TEXT NOT NULL DEFAULT '',
    email TEXT NOT NULL DEFAULT '',
    modulelist TEXT NOT NULL
);

-- Table: sino_book
CREATE TABLE sino_book (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    typeid INTEGER NOT NULL DEFAULT 0,
    userid INTEGER NOT NULL DEFAULT 0,
    user TEXT NOT NULL DEFAULT '',
    subject TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    email TEXT NOT NULL DEFAULT '',
    ifcheck INTEGER NOT NULL DEFAULT 0,
    language INTEGER NOT NULL DEFAULT 0,
    reply TEXT NOT NULL,
    replydate INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_category
CREATE TABLE sino_category (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sysgroupid INTEGER NOT NULL DEFAULT 0,
    rootid INTEGER NOT NULL DEFAULT 0,
    parentid INTEGER NOT NULL DEFAULT 0,
    catename TEXT NOT NULL DEFAULT '',
    catestyle TEXT NOT NULL DEFAULT '',
    taxis INTEGER NOT NULL DEFAULT 255,
    tpl_index TEXT NOT NULL DEFAULT '',
    tpl_list TEXT NOT NULL DEFAULT '',
    tpl_msg TEXT NOT NULL DEFAULT '',
    note TEXT NOT NULL DEFAULT '',
    status INTEGER NOT NULL DEFAULT 1,
    language INTEGER NOT NULL DEFAULT 0,
    psize INTEGER NOT NULL DEFAULT 30,
    isrefreshcount INTEGER NOT NULL DEFAULT 1,
    msgcount INTEGER NOT NULL DEFAULT 0,
    showtype INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_feedback
CREATE TABLE sino_feedback (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    userid INTEGER NOT NULL DEFAULT 0,
    subject TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    reply TEXT NOT NULL,
    replydate INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_job
CREATE TABLE sino_job (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    jobname TEXT NOT NULL DEFAULT '',
    usercount INTEGER NOT NULL DEFAULT 0,
    age TEXT NOT NULL DEFAULT '',
    experience TEXT NOT NULL DEFAULT '',
    height TEXT NOT NULL DEFAULT '',
    gender INTEGER NOT NULL DEFAULT 0,
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    enddate INTEGER NOT NULL DEFAULT 0,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_jobapp
CREATE TABLE sino_jobapp (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    jobid INTEGER NOT NULL DEFAULT 0,
    userid INTEGER NOT NULL DEFAULT 0,
    jobname TEXT NOT NULL DEFAULT '',
    name TEXT NOT NULL DEFAULT '',
    age INTEGER NOT NULL DEFAULT 0,
    height INTEGER NOT NULL DEFAULT 150,
    schoolage TEXT NOT NULL DEFAULT '',
    contact TEXT NOT NULL DEFAULT '',
    photo TEXT NOT NULL DEFAULT '',
    note TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_lang
CREATE TABLE sino_lang (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sign TEXT NOT NULL DEFAULT '',
    name TEXT NOT NULL DEFAULT '',
    ifuse INTEGER NOT NULL DEFAULT 0,
    ifdefault INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_link
CREATE TABLE sino_link (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    typeid INTEGER NOT NULL DEFAULT 0,
    sitename TEXT NOT NULL DEFAULT '',
    siteurl TEXT NOT NULL DEFAULT '',
    logo TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    status INTEGER NOT NULL DEFAULT 0,
    taxis INTEGER NOT NULL DEFAULT 255,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_msg
CREATE TABLE sino_msg (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    typeid INTEGER NOT NULL DEFAULT 0,
    sysgroupid INTEGER NOT NULL DEFAULT 0,
    title TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    hits INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 1,
    taxis INTEGER NOT NULL DEFAULT 255,
    language INTEGER NOT NULL DEFAULT 0,
    ishtml INTEGER NOT NULL DEFAULT 0,
    isurl INTEGER NOT NULL DEFAULT 0,
    url TEXT NOT NULL DEFAULT '',
    tags TEXT NOT NULL DEFAULT '',
    keywords TEXT NOT NULL DEFAULT '',
    description TEXT NOT NULL DEFAULT '',
    author TEXT NOT NULL DEFAULT '',
    source TEXT NOT NULL DEFAULT '',
    filename TEXT NOT NULL DEFAULT '',
    iscomment INTEGER NOT NULL DEFAULT 1,
    isgood INTEGER NOT NULL DEFAULT 0,
    istop INTEGER NOT NULL DEFAULT 0,
    ispic INTEGER NOT NULL DEFAULT 0,
    pic TEXT NOT NULL DEFAULT '',
    thumb TEXT NOT NULL DEFAULT '',
    pic_link TEXT NOT NULL DEFAULT '',
    isvote INTEGER NOT NULL DEFAULT 0,
    voteid INTEGER NOT NULL DEFAULT 0,
    specialid INTEGER NOT NULL DEFAULT 0,
    ismsg INTEGER NOT NULL DEFAULT 0,
    msgid INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_msgdata
CREATE TABLE sino_msgdata (
    id INTEGER NOT NULL PRIMARY KEY,
    content TEXT NOT NULL
);

-- Table: sino_nav
CREATE TABLE sino_nav (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parentid INTEGER NOT NULL DEFAULT 0,
    name TEXT NOT NULL DEFAULT '',
    url TEXT NOT NULL DEFAULT '',
    target TEXT NOT NULL DEFAULT '',
    taxis INTEGER NOT NULL DEFAULT 255,
    status INTEGER NOT NULL DEFAULT 1,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_online
CREATE TABLE sino_online (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    userid INTEGER NOT NULL DEFAULT 0,
    user TEXT NOT NULL DEFAULT '',
    ip TEXT NOT NULL DEFAULT '',
    postdate INTEGER NOT NULL DEFAULT 0,
    lastdate INTEGER NOT NULL DEFAULT 0,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_order
CREATE TABLE sino_order (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ordernum TEXT NOT NULL DEFAULT '',
    userid INTEGER NOT NULL DEFAULT 0,
    user TEXT NOT NULL DEFAULT '',
    email TEXT NOT NULL DEFAULT '',
    tel TEXT NOT NULL DEFAULT '',
    address TEXT NOT NULL,
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 0,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_orderlist
CREATE TABLE sino_orderlist (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    orderid INTEGER NOT NULL DEFAULT 0,
    msgid INTEGER NOT NULL DEFAULT 0,
    title TEXT NOT NULL DEFAULT '',
    price REAL NOT NULL DEFAULT 0,
    quantity INTEGER NOT NULL DEFAULT 0,
    subtotal REAL NOT NULL DEFAULT 0
);

-- Table: sino_session
CREATE TABLE sino_session (
    id TEXT NOT NULL PRIMARY KEY,
    data TEXT NOT NULL,
    time INTEGER NOT NULL
);

-- Table: sino_special
CREATE TABLE sino_special (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    hits INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 1,
    taxis INTEGER NOT NULL DEFAULT 255,
    language INTEGER NOT NULL DEFAULT 0,
    pic TEXT NOT NULL DEFAULT '',
    thumb TEXT NOT NULL DEFAULT '',
    filename TEXT NOT NULL DEFAULT ''
);

-- Table: sino_style
CREATE TABLE sino_style (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    taxis INTEGER NOT NULL DEFAULT 255,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_sysgroup
CREATE TABLE sino_sysgroup (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL DEFAULT '',
    taxis INTEGER NOT NULL DEFAULT 255,
    ifuse INTEGER NOT NULL DEFAULT 1,
    note TEXT NOT NULL DEFAULT '',
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_system
CREATE TABLE sino_system (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_tpl
CREATE TABLE sino_tpl (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tplid INTEGER NOT NULL DEFAULT 0,
    name TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_upfiles
CREATE TABLE sino_upfiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL DEFAULT '',
    filepath TEXT NOT NULL DEFAULT '',
    filesize INTEGER NOT NULL DEFAULT 0,
    filetype TEXT NOT NULL DEFAULT '',
    postdate INTEGER NOT NULL DEFAULT 0,
    hits INTEGER NOT NULL DEFAULT 0,
    ispic INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_user
CREATE TABLE sino_user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user TEXT NOT NULL DEFAULT '',
    pass TEXT NOT NULL DEFAULT '',
    email TEXT NOT NULL DEFAULT '',
    tel TEXT NOT NULL DEFAULT '',
    address TEXT NOT NULL,
    regdate INTEGER NOT NULL DEFAULT 0,
    lastdate INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 1,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_vote
CREATE TABLE sino_vote (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL DEFAULT '',
    content TEXT NOT NULL,
    postdate INTEGER NOT NULL DEFAULT 0,
    hits INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 1,
    taxis INTEGER NOT NULL DEFAULT 255,
    language INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_voteitem
CREATE TABLE sino_voteitem (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    voteid INTEGER NOT NULL DEFAULT 0,
    title TEXT NOT NULL DEFAULT '',
    count INTEGER NOT NULL DEFAULT 0,
    taxis INTEGER NOT NULL DEFAULT 255
);

-- Table: sino_voterec
CREATE TABLE sino_voterec (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    voteid INTEGER NOT NULL DEFAULT 0,
    itemid INTEGER NOT NULL DEFAULT 0,
    ip TEXT NOT NULL DEFAULT '',
    postdate INTEGER NOT NULL DEFAULT 0
);

-- Table: sino_xu_upfiles
CREATE TABLE sino_xu_upfiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL DEFAULT '',
    filepath TEXT NOT NULL DEFAULT '',
    filesize INTEGER NOT NULL DEFAULT 0,
    filetype TEXT NOT NULL DEFAULT '',
    postdate INTEGER NOT NULL DEFAULT 0,
    hits INTEGER NOT NULL DEFAULT 0,
    ispic INTEGER NOT NULL DEFAULT 0,
    thumb TEXT NOT NULL DEFAULT ''
);

-- Insert default data
INSERT INTO sino_lang (sign, name, ifuse, ifdefault) VALUES 
('zh', '简体中文', 1, 1),
('en', 'English', 1, 0);

INSERT INTO sino_admin (typer, user, pass, email) VALUES 
('system', 'admin', '784d22bd57dbe6561786811e98724848', 'admin@example.com');

INSERT INTO sino_nav (name, url, taxis, status, language) VALUES 
('首页', 'index.php', 1, 1, 1),
('产品中心', 'list.php?id=10', 2, 1, 1),
('新闻资讯', 'list.php?id=40', 3, 1, 1),
('联系我们', 'msg.php?id=1', 4, 1, 1);

-- Create indexes
CREATE INDEX idx_sino_ad_typeid ON sino_ad(typeid);
CREATE INDEX idx_sino_ad_dates ON sino_ad(start_date, end_date, status);
CREATE INDEX idx_sino_book_language ON sino_book(language);
CREATE INDEX idx_sino_book_typeid ON sino_book(typeid);
CREATE INDEX idx_sino_category_status_lang ON sino_category(status, language);
CREATE INDEX idx_sino_category_rootid ON sino_category(rootid, status, language);
CREATE INDEX idx_sino_category_parentid ON sino_category(parentid, status, language);
CREATE INDEX idx_sino_job_language ON sino_job(language);
CREATE INDEX idx_sino_msg_typeid ON sino_msg(typeid);
CREATE INDEX idx_sino_msg_status_lang ON sino_msg(status, language);
CREATE INDEX idx_sino_msg_taxis ON sino_msg(taxis);
CREATE INDEX idx_sino_msg_postdate ON sino_msg(postdate);
CREATE INDEX idx_sino_msg_hits ON sino_msg(hits);
CREATE INDEX idx_sino_msg_sysgroupid ON sino_msg(sysgroupid);
CREATE INDEX idx_sino_msg_language ON sino_msg(language);
CREATE INDEX idx_sino_msg_isgood ON sino_msg(isgood);
CREATE INDEX idx_sino_msg_istop ON sino_msg(istop);
CREATE INDEX idx_sino_msg_specialid ON sino_msg(specialid);
CREATE INDEX idx_sino_msg_voteid ON sino_msg(voteid);
CREATE INDEX idx_sino_order_postdate ON sino_order(postdate);
CREATE INDEX idx_sino_order_status ON sino_order(status);
CREATE INDEX idx_sino_order_language ON sino_order(language);
CREATE INDEX idx_sino_order_userid ON sino_order(userid);
CREATE INDEX idx_sino_upfiles_postdate ON sino_upfiles(postdate);
CREATE INDEX idx_sino_upfiles_hits ON sino_upfiles(hits);
CREATE INDEX idx_sino_upfiles_ispic ON sino_upfiles(ispic);
CREATE INDEX idx_sino_user_status ON sino_user(status);
CREATE INDEX idx_sino_user_language ON sino_user(language);
CREATE INDEX idx_sino_vote_status_lang ON sino_vote(status, language);
CREATE INDEX idx_sino_vote_taxis ON sino_vote(taxis);
CREATE INDEX idx_sino_voteitem_voteid ON sino_voteitem(voteid);
CREATE INDEX idx_sino_voteitem_taxis ON sino_voteitem(taxis);