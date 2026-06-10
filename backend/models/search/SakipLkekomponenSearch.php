<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipLkekomponen;

/**
 * SakipLkekomponenSearch represents the model behind the search form of `backend\models\SakipLkekomponen`.
 */
class SakipLkekomponenSearch extends SakipLkekomponen
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflkekomponen_id'], 'integer'],
            [['uraian_lkekomponen'], 'safe'],
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
        $query = SakipLkekomponen::find();

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
            'reflkekomponen_id' => $this->reflkekomponen_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_lkekomponen', $this->uraian_lkekomponen]);

        return $dataProvider;
    }
}
