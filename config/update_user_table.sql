-- 添加last_login字段到users表
ALTER TABLE users ADD COLUMN last_login DATETIME NULL DEFAULT NULL COMMENT '用户最后登录时间';

-- 为现有用户更新last_login为当前时间
UPDATE users SET last_login = NOW() WHERE last_login IS NULL; 