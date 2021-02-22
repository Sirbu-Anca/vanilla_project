create table orders
(
    id            int unsigned auto_increment
        primary key,
    creation_date datetime     not null,
    name          varchar(255) not null,
    address       varchar(255) not null,
    comments      varchar(255) null
);

create table products
(
    id          int unsigned auto_increment
        primary key,
    title       varchar(255) not null,
    description varchar(255) not null,
    price       float        not null,
    image       varchar(255) not null
);

create table order_products
(
    id            int unsigned auto_increment
        primary key,
    order_id      int unsigned not null,
    product_id    int unsigned not null,
    product_price float        not null,
    constraint order_products_orders__fk
        foreign key (order_id) references orders (id)
            on update cascade on delete cascade,
    constraint order_products_products__fk
        foreign key (product_id) references products (id)
            on update cascade on delete cascade
);

create table reviews
(
    id            int unsigned auto_increment
        primary key,
    product_id    int unsigned not null,
    comment       varchar(255) not null,
    rating        int          not null,
    creation_date datetime     not null,
    constraint reviews_products__fk
        foreign key (product_id) references products (id)
            on update cascade on delete cascade
);


