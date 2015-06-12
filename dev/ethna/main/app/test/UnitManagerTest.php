<?php

class UnitManager_TestCase extends Ethna_UnitTestCase {

    private $manager;

    /**
     * initialize test.
     *
     * @access public
     */
    function setUp()
    {
        $this->manager =& $this->backend->getManager('Unit');
        // TODO: write test initialization code.
        // Example: read test data from database.
    }

    /**
     *  clean up testcase.
     *
     *  @access public
     */
    function tearDown()
    {
        // TODO: write testcase cleanup code.
        // Example: restore database data for development.
    }


    /**
     * GetAllMultiUnitのテスト（正常系）
     */
    function test_GetAllMultiUnit() {

        $sql = "select * from t_user_base";

        $unit_all = $this->backend->config->get('unit_all');
        $con = NewADOConnection($unit_all[1]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $ret = $con->GetAll($sql, array());
        $unit1Cnt = count($ret);

        $con = NewADOConnection($unit_all[2]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $ret = $con->GetAll($sql, array());
        $unit2Cnt = count($ret);

        $sumCount = $unit1Cnt + $unit2Cnt;

        $unit_m = $this->backend->getManager('Unit');
        $ret = $unit_m->getAllMultiUnit($sql, array(),NULL, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生しているか");
        $this->assertEqual($sumCount, count($ret), "件数が一致するか(BIQ_SELECT=OFF)");

        $ret = $unit_m->getAllMultiUnit($sql, array(),NULL, true);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生しているか");
        $this->assertEqual($sumCount, count($ret), "件数が一致するか(BIQ_SELECT=ON)");

        $ret = $unit_m->getAllMultiUnit($sql, array(), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生しているか");
        $this->assertEqual($unit1Cnt, count($ret), "件数が一致するか(UNIT1)");

        $ret = $unit_m->getAllMultiUnit($sql, array(), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生しているか");
        $this->assertEqual($unit2Cnt, count($ret), "件数が一致するか(UNIT2)");
    }

    /**
     * GetAllMultiUnitのテスト（異常系）
     */
    function test_GetAllMultiUnit_illegal() {
        $errSql = "select * from tt_user_base";

        $unit_m = $this->backend->getManager('Unit');
        $ret = $unit_m->getAllMultiUnit($errSql, array(),NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");

        $ret = $unit_m->getAllMultiUnit($errSql, array(),NULL, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");

        $ret = $unit_m->getAllMultiUnit($errSql, array(), 1, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");

        $ret = $unit_m->getAllMultiUnit($errSql, array(), 2, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");

    }


    /**
     * getAllSpecificUnitの内容をテスト（正常系）
     */
    function test_getAllSpecificUnit() {

        $sql = "select * from t_user_base";

        $unit_all = $this->backend->config->get('unit_all');
        $con = NewADOConnection($unit_all[1]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $ret = $con->GetAll($sql, array());
        $unit1Cnt = count($ret);

        $con = NewADOConnection($unit_all[2]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $ret = $con->GetAll($sql, array());
        $unit2Cnt = count($ret);

        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getAllSpecificUnit($sql, array(), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit1Cnt, count($ret), "件数が一致するか(BIQ_SELECT=OFF)");

        $unit_m = $this->backend->getManager('Unit');
        $ret = $unit_m->getAllSpecificUnit($sql, array(), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit2Cnt, count($ret), "件数が一致するか(BIQ_SELECT=OFF)");


    }

    /**
     * getAllSpecificUnitの内容をテスト（異常系）
     */
    function test_getAllSpecificUnit_illelal() {
        $errSql = "select * from tt_user_base"; // table not found query.
        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getAllSpecificUnit($errSql, array(), 1, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");

        $ret = $unit_m->getAllSpecificUnit($errSql, array(), 2, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生しているか");
    }


    /**
     * getRowMultiUnitの内容をテスト（正常系）
     */
    function test_getRowMultiUnit() {

        $unit1User = 100001792;
        $unit2User = 100001818;
        $sql = "select * from t_user_base where user_id = ?";

        $unit_all = $this->backend->config->get('unit_all');
        $con = NewADOConnection($unit_all[1]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $row1 = $con->GetRow($sql, array($unit1User));

        $con = NewADOConnection($unit_all[2]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $row2 = $con->GetRow($sql, array($unit2User));

        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getRowMultiUnit($sql, array($unit1User), NULL, true);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($row1, $ret, "取得結果が一致しているか");

        $ret = $unit_m->getRowMultiUnit($sql, array($unit2User), NULL, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($row2, $ret, "取得結果が一致しているか");

        $ret = $unit_m->getRowMultiUnit($sql, array($unit1User), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($row1, $ret, "取得結果が一致しているか");

        $ret = $unit_m->getRowMultiUnit($sql, array($unit2User), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual(array(), $ret, "ユーザーがとれない");


        $ret = $unit_m->getRowMultiUnit($sql, array($unit2User), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($row2, $ret, "取得結果が一致しているか");

        $ret = $unit_m->getRowMultiUnit($sql, array($unit1User), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual(array(), $ret, "ユーザーがとれない");

    }

    /**
     * getRowMultiUnitの内容をテスト（異常系）
     */
    function test_getRowMultiUnit_illegal() {
        $unit1User = 100001792;
        $unit2User = 100001818;
        $errSql = "select * from tt_user_base where user_id = ?";// table not found query.

        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit1User), NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit1User), NULL, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit1User), 1, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit1User), 2, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit2User), NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit2User), NULL, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit2User), 1, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

        $ret = $unit_m->getRowMultiUnit($errSql, array($unit2User), 2, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生している");

    }


    /**
     * GetOneを実装（正常系）
     */
    function test_GetOneMultiUnit() {

        $unit1User = 100001792;
        $unit2User = 100001818;
        $sql = "select name from t_user_base where user_id = ?";

        $unit_all = $this->backend->config->get('unit_all');
        $con = NewADOConnection($unit_all[1]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $unit1UserName = $con->GetOne($sql, array($unit1User));

        $unit_all = $this->backend->config->get('unit_all');
        $con = NewADOConnection($unit_all[2]['dsn']);
        $con->Execute("SET NAMES utf8mb4");
        $unit2UserName = $con->GetOne($sql, array($unit2User));

        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getOneMultiUnit($sql, array($unit1User), NULL, true);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit1UserName, $ret, "取得結果が一致しているか1");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit1User), NULL, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit1UserName, $ret, "取得結果が一致しているか2");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit2User), NULL, true);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit2UserName, $ret, "取得結果が一致しているか3");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit2User), NULL, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit2UserName, $ret, "取得結果が一致しているか4");


        $ret = $unit_m->getOneMultiUnit($sql, array($unit1User), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit1UserName, $ret, "取得結果が一致しているか5");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit2User), 1, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertNULL($ret, "取得結果がNULLかどうか6");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit1User), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertNULL($ret, "取得結果がNULLかどうか7");

        $ret = $unit_m->getOneMultiUnit($sql, array($unit2User), 2, false);
        $this->assertFalse(Ethna::isError($ret), "EthnaError発生していない");
        $this->assertEqual($unit2UserName, $ret, "取得結果が一致しているか8");

    }

    /**
     * GetOneを実装（異常系）
     */
    function test_GetOneMultiUnit_illegal() {
        $unit1User = 100001792;
        $unit2User = 100001818;
        $errSql = "select name from tt_user_base where user_id = ?";

        $unit_m = $this->backend->getManager('Unit');

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), NULL, true);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), NULL, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), 1, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

        $ret = $unit_m->getOneMultiUnit($errSql, array($unit1User), 2, false);
        $this->assertTrue(Ethna::isError($ret), "EthnaError発生していない");

    }


    /**
     * 指定したユニットに対してExecuteクエリを実行するテスト
     */
    function test_executeForUnit() {
        $unit = 1;
        $sql = "update t_user_base set login_date = now() where user_id = 100001793";
        $errSql = "update t_user_base set login_date = now() where user_id = a0";
        $param = array();

        $ret = $this->manager->executeForUnit($unit, $sql, $param);
        $this->assertFalse($ret->ErrorNo, "[同一ユニット] 正常終了するか");
        $this->assertEqual($ret->affected_rows, 1, "[同一ユニット] 影響した行が正しいか");

        $ret = $this->manager->executeForUnit($unit, $errSql, $param);
        $this->assertTrue($ret->ErrorNo, "[同一ユニット]エラー終了するか");
        $this->assertTrue($ret, "[同一ユニット]エラーコードが正しいか");
        $this->dump($ret->ErrorNo, "[同一ユニット] エラーメッセージ確認");
        $this->dump($ret->ErrorMsg, "[同一ユニット] エラーメッセージ確認");

        $unit = 2;
        $ret = $this->manager->executeForUnit($unit, $sql, $param);
        $this->assertFalse($ret->ErrorNo, "[別ユニット] 正常終了するか");
        $this->assertEqual($ret->affected_rows, 1, "[別ユニット] 影響した行が正しいか");

        $ret = $this->manager->executeForUnit($unit, $errSql, $param);
        $this->assertTrue($ret->ErrorNo, "[別ユニット] エラー終了するか");
        $this->assertTrue($ret, "[別ユニット] エラーコードが正しいか");
        $this->dump($ret->ErrorNo, "[別ユニット] エラーメッセージ確認");
        $this->dump($ret->ErrorMsg, "[別ユニット] エラーメッセージ確認");

    }


    function test_unitFromUserIdList() {
        $user_id_list = array(100001793,100001818,100001796,100001802);
        $ret = $this->manager->cacheGetUnitFromUserIdList($user_id_list);
        $this->dump($ret);
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }


}