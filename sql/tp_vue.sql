
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log`  (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '操作用户',
  `date_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-单条数据，2-多条数据',
  `table_name` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作表单名',
  `table_id` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作表单ID',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作类型（0-无，10-增，20-改，30-删）',
  `detail` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作详情',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_log
-- ----------------------------
INSERT INTO `admin_log` VALUES (1, 1, 1, 'admin_user', '2', 20, '编辑后台用户', 1625991299);

-- ----------------------------
-- Table structure for admin_permission
-- ----------------------------
DROP TABLE IF EXISTS `admin_permission`;
CREATE TABLE `admin_permission`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型（10-菜单，20-页面，25-页面&按钮，30-按钮）',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色权限列表标题',
  `path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '前端页面路由',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端页面组件',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端菜单/页面名称',
  `icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '前端菜单图标',
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '前端菜单是否隐藏',
  `permission` json NOT NULL COMMENT '前后端权限标识',
  `sort` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `selectable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '角色权限列表是否可选择',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态（0-禁用，1-启用）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permission
-- ----------------------------
INSERT INTO `admin_permission` VALUES (1, 0, 10, '权限管理', '/it', 'Layout', 'IT', 'lock', 0, '[]', 99, 1, 1);
INSERT INTO `admin_permission` VALUES (2, 1, 10, '用户管理-列表', 'user', 'permission/user/index', 'PermissionUserManage', '', 0, '[\"user:list\"]', 10, 1, 1);
INSERT INTO `admin_permission` VALUES (3, 1, 25, '用户管理-添加', 'user/add', 'permission/user/add', 'PermissionUserManageAdd', '', 1, '[\"user:add\"]', 11, 1, 1);
INSERT INTO `admin_permission` VALUES (4, 1, 25, '用户管理-编辑', 'user/edit/:id', 'permission/user/edit', 'PermissionUserManageEdit', '', 1, '[\"user:edit\"]', 12, 1, 1);
INSERT INTO `admin_permission` VALUES (5, 1, 30, '用户管理-删除', '', '', '', '', 0, '[\"user:delete\"]', 13, 1, 1);
INSERT INTO `admin_permission` VALUES (6, 1, 10, '角色管理-列表', 'role', 'permission/role/index', 'PermissionRoleManage', '', 0, '[\"role:list\"]', 20, 1, 1);
INSERT INTO `admin_permission` VALUES (7, 1, 30, '角色管理-添加', '', '', '', '', 0, '[\"role:add\"]', 21, 1, 1);
INSERT INTO `admin_permission` VALUES (8, 1, 30, '角色管理-编辑', '', '', '', '', 0, '[\"role:edit\"]', 22, 1, 1);
INSERT INTO `admin_permission` VALUES (9, 1, 30, '角色管理-删除', '', '', '', '', 0, '[\"role:delete\"]', 23, 1, 1);
INSERT INTO `admin_permission` VALUES (10, 1, 10, '权限管理-列表', 'permission', 'permission/permission/index', 'PermissionPermissionManage', '', 0, '[\"permission:list\"]', 30, 0, 1);
INSERT INTO `admin_permission` VALUES (11, 1, 30, '权限管理-添加', '', '', '', '', 0, '[\"permission:add\"]', 31, 0, 1);
INSERT INTO `admin_permission` VALUES (12, 1, 30, '权限管理-编辑', '', '', '', '', 0, '[\"permission:edit\"]', 32, 0, 1);
INSERT INTO `admin_permission` VALUES (13, 1, 30, '权限管理-删除', '', '', '', '', 0, '[\"permission:delete\"]', 33, 0, 1);
INSERT INTO `admin_permission` VALUES (14, 0, 10, '系统管理', '/system', 'Layout', 'SystemManage', 'cog-fill', 0, '[\"\"]', 98, 1, 1);
INSERT INTO `admin_permission` VALUES (15, 14, 10, '操作日志', 'log', 'system/log', 'OperationLog', '', 0, '[\"log:list\"]', 10, 1, 1);
INSERT INTO `admin_permission` VALUES (16, 14, 10, '缓存管理', 'cache', 'system/cache', 'CacheManage', '', 0, '[\"cache:list\", \"cache:delete\"]', 20, 1, 1);
INSERT INTO `admin_permission` VALUES (17, 0, 10, 'Demo', '/demo', 'Layout', 'Demo', 'list', 0, '[]', 10, 1, 1);
INSERT INTO `admin_permission` VALUES (18, 17, 10, 'Demo-列表', 'index', 'demo/index', 'DemoList', '', 0, '[\"demo:list\"]', 10, 1, 1);
INSERT INTO `admin_permission` VALUES (19, 17, 25, 'Demo-添加', 'add', 'demo/add', 'DemoAdd', '', 1, '[\"demo:add\"]', 11, 1, 1);
INSERT INTO `admin_permission` VALUES (20, 17, 25, 'Demo-編輯', 'edit/:id', 'demo/edit', 'DemoEdit', '', 1, '[\"demo:edit\"]', 12, 1, 1);
INSERT INTO `admin_permission` VALUES (21, 17, 30, 'Demo-刪除', '', '', '', '', 0, '[\"demo:delete\"]', 13, 1, 1);
INSERT INTO `admin_permission` VALUES (22, 17, 30, 'Demo-导入', '', '', '', '', 0, '[\"demo:import\"]', 14, 1, 1);
INSERT INTO `admin_permission` VALUES (23, 17, 30, 'Demo-导出', '', '', '', '', 0, '[\"demo:export\"]', 15, 1, 1);

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色名称',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色描述',
  `permission` json NOT NULL COMMENT '权限数组（admin_permission）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role
-- ----------------------------
INSERT INTO `admin_role` VALUES (1, 'admin', '超级管理员，拥有所有权限', '[]');
INSERT INTO `admin_role` VALUES (2, '测试角色', '權限測試', '[\"17\", \"18\", \"19\", \"20\", \"21\", \"22\", \"23\", \"14\", \"15\", \"16\"]');

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `last_login` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `last_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `role_id` int(11) NOT NULL DEFAULT 0 COMMENT '角色id',
  `last_fail_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登录失败时间',
  `login_fail_times` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录失败次数',
  `is_lock` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否锁定',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否在职（1-是，0-否）',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES (1, 'admin', 'd93a5def7511da3d0f2d171d9c344e91', '超级管理员', '', 1625980012, '127.0.0.1', 1, 0, 0, 0, 1, 1574149119, 1625980012);
INSERT INTO `admin_user` VALUES (2, 'test', 'd93a5def7511da3d0f2d171d9c344e91', '测试', '', 1625816773, '127.0.0.1', 2, 0, 0, 0, 1, 1610356707, 1625991299);

-- ----------------------------
-- Table structure for demo
-- ----------------------------
DROP TABLE IF EXISTS `demo`;
CREATE TABLE `demo`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型（1-DEMO_1，2-DEMO_2）',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of demo
-- ----------------------------
INSERT INTO `demo` VALUES (1, 1, '测试1', '&lt;p&gt;测试1&lt;/p&gt;', 'uploads/common/20210711/ec1ea5e1d969771623704fb59148370b.jpg', 1625985922, 1625985922);
INSERT INTO `demo` VALUES (2, 1, '测试2', NULL, '', 1625988276, 1625988276);
INSERT INTO `demo` VALUES (3, 2, '测试3', NULL, '', 1625988276, 1625988276);

-- ----------------------------
-- Table structure for table_dictionary
-- ----------------------------
DROP TABLE IF EXISTS `table_dictionary`;
CREATE TABLE `table_dictionary`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '表单',
  `field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段',
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '键',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '值',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `table_field`(`table`, `field`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of table_dictionary
-- ----------------------------
INSERT INTO `table_dictionary` VALUES (1, 'demo', 'type', '1', '测试类型_1', 0, 0);
INSERT INTO `table_dictionary` VALUES (2, 'demo', 'type', '2', '测试类型_2', 0, 0);

SET FOREIGN_KEY_CHECKS = 1;
