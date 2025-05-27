# users are intended as the barbers that will manage the platform
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
    issued_at datetime not null,
    expires_at datetime not null,
    created_at timestamp default current_timestamp
);

create table users_oauth_refresh_token (
    user_oauth_refresh_token_id int primary key auto_increment,
    user_id int not null references users(user_id),
    refresh_token longtext not null,
    issued_at datetime not null,
    expires_at datetime not null,
    created_at timestamp default current_timestamp
);

create table services (
    service_id int primary key auto_increment,
    service_name varchar(255) not null,
    description text,
    price int not null,
    duration int not null, -- duration in minutes
    created_at timestamp default current_timestamp
);

create table reservations (
    reservation_id int primary key auto_increment,
    user_id int not null references users(user_id),
    reservation_date date not null,
    start_time time not null,
    end_time time not null,
    description varchar(255),
    phone_number varchar(20),
    status enum('pending', 'confirmed', 'cancelled', 'completed') default 'pending',
    payment_type enum('cash', 'card', 'satispay'),
    created_at timestamp default current_timestamp
);

create table reservations_items (
    reservation_item_id int primary key auto_increment,
    reservation_id int not null references reservations(reservation_id),
    service_id int not null references services(service_id),
    quantity int not null default 1,
    created_at timestamp default current_timestamp
);