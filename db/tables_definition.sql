create table users (
    user_id int primary key auto_increment,
    user_name varchar(255) not null,
    email varchar(255) not null unique,
    password varchar(255) not null,
    created_at timestamp default current_timestamp
);

create table users_oauth_token (
    user_oauth_token_id int primary key auto_increment,
    user_id int not null references users(user_id),
    token longtext not null,
    issued_at varchar(255) not null,
    expires_at varchar(255) not null,
    created_at timestamp default current_timestamp
);