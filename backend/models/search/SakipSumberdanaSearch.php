<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSumberdana;

/**
 * SakipSumberdanaSearch represents the model behind the search form of `backend\models\SakipSumberdana`.
 */
class SakipSumberdanaSearch extends SakipSumberdana
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsumberdana_id'], 'integer'],
            [['kode_sumberdana', 'nama_sumberdana', 'sumberdana_isaktif'], 'safe'],
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
        $query = SakipSumberdana::find();

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
            'refsumberdana_id' => $this->refsumberdana_id,
        ]);

        $query->andFilterWhere(['like', 'kode_sumberdana', $this->kode_sumberdana])
            ->andFilterWhere(['like', 'nama_sumberdana', $this->nama_sumberdana])
            ->andFilterWhere(['like', 'sumberdana_isaktif', $this->sumberdana_isaktif]);

        return $dataProvider;
    }
}
