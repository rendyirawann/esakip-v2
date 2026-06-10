<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipPenanggungjawab;

/**
 * SakipPenanggungjawabSearch represents the model behind the search form of `backend\models\SakipPenanggungjawab`.
 */
class SakipPenanggungjawabSearch extends SakipPenanggungjawab
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenanggungjawab_id', 'refpegawai_id', 'refbidangbappeda_id', 'refuser_id', 'refskpd_id'], 'integer'],
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
        $query = SakipPenanggungjawab::find();

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
            'refpenanggungjawab_id' => $this->refpenanggungjawab_id,
            'refpegawai_id' => $this->refpegawai_id,
            'refbidangbappeda_id' => $this->refbidangbappeda_id,
            'refuser_id' => $this->refuser_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        return $dataProvider;
    }
}
