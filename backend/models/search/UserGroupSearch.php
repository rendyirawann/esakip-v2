<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserGroup;

/**
 * UserGroupSearch represents the model behind the search form of `backend\models\UserGroup`.
 */
class UserGroupSearch extends UserGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_group'], 'integer'],
            [['nama_group'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserGroup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'kode_group' => $this->kode_group,
        ]);

        $query->andFilterWhere(['like', 'nama_group', $this->nama_group]);

        return $dataProvider;
    }
}
