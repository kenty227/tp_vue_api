# (THINKPHP + VUE) 接口基础框架

当前版本基于 ThinkPHP 5.1。

项目对应后台见 [后台仓库](https://github.com/kenty227/tp_vue_admin) 。

---

## Build Setup

#### 1. 安装依赖 `composer install`

#### 2. 导入 /sql/tp_vue.sql 文件至 MySQL数据库（MySQL >= 5.7）

#### 3. 复制 `.env.dev` 配置文件为 `.env`, 修改相应配置

---

## Release

> 部署/更新前请务必先执行以下命令（详细操作见 /composer.json）

```bash
# 构建测试环境
composer run-script build-dev

# 构建生产环境
composer run-script build-prod
```
