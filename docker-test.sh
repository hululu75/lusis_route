#!/bin/bash

echo "🧪 Docker配置测试脚本"
echo "===================="
echo ""

# 颜色定义
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 测试计数
PASSED=0
FAILED=0

# 测试函数
test_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASSED${NC}: $2"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAILED${NC}: $2"
        ((FAILED++))
    fi
}

echo "📋 测试1: 检查Dockerfile是否包含所有数据库扩展"
if grep -q "pdo_pgsql" Dockerfile; then
    test_result 0 "Dockerfile包含pdo_pgsql"
else
    test_result 1 "Dockerfile缺少pdo_pgsql"
fi

if grep -q "libpq-dev" Dockerfile; then
    test_result 0 "Dockerfile包含libpq-dev"
else
    test_result 1 "Dockerfile缺少libpq-dev"
fi

if ! grep -q "\-\-no-dev" Dockerfile; then
    test_result 0 "Dockerfile不使用--no-dev"
else
    test_result 1 "Dockerfile仍使用--no-dev"
fi

echo ""
echo "📋 测试2: 检查.env.docker.example配置"
if grep -q "^APP_KEY=base64:" .env.docker.example; then
    test_result 0 ".env.docker.example包含有效APP_KEY"
else
    test_result 1 ".env.docker.example缺少有效APP_KEY"
fi

echo ""
echo "📋 测试3: 检查docker-compose.yml配置"
if grep -q "# depends_on:" docker-compose.yml; then
    test_result 0 "docker-compose.yml的depends_on已注释"
else
    test_result 1 "docker-compose.yml的depends_on未注释"
fi

if grep -q "postgres:" docker-compose.yml; then
    test_result 0 "docker-compose.yml包含PostgreSQL服务"
else
    test_result 1 "docker-compose.yml缺少PostgreSQL服务"
fi

if grep -q "profiles:" docker-compose.yml; then
    test_result 0 "docker-compose.yml使用profiles"
else
    test_result 1 "docker-compose.yml未使用profiles"
fi

echo ""
echo "📋 测试4: 检查必需文件"
files=(
    "Dockerfile"
    "docker-compose.yml"
    ".env.docker.example"
    ".dockerignore"
    "DOCKER_GUIDE.md"
    "DOCKER_QUICK_REFERENCE.md"
    "DOCKER_FIXES.md"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        test_result 0 "$file 存在"
    else
        test_result 1 "$file 不存在"
    fi
done

echo ""
echo "===================="
echo "测试结果总结"
echo "===================="
echo -e "通过: ${GREEN}${PASSED}${NC}"
echo -e "失败: ${RED}${FAILED}${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ 所有测试通过！${NC}"
    echo ""
    echo "Docker配置已就绪，可以使用："
    echo "  docker compose up -d                    # SQLite模式"
    echo "  docker compose --profile postgres up -d # PostgreSQL模式"
    exit 0
else
    echo -e "${RED}✗ 有 $FAILED 个测试失败${NC}"
    echo ""
    echo "请检查上述失败的测试"
    exit 1
fi
