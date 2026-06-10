<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorcascadingprogram;

/**
 * SakipIndikatorcascadingprogramSearch represents the model behind the search form of `frontend\models\SakipIndikatorcascadingprogram`.
 */
class SakipIndikatorcascadingprogramSearch extends SakipIndikatorcascadingprogram
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorprogram_id', 'refcascadingprogram_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'keterangan', 'keterangan_pk', 'keterangan_pk_p', 'analisis'], 'safe'],
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
        $query = SakipIndikatorcascadingprogram::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refCascadingProgram']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refprogram_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refindikatorprogram_id' => $this->refindikatorprogram_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'refbidang_id' => $this->refbidang_id,
            'refprogram_id' => $this->refprogram_id,
        ]);

        $query->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'keterangan_pk', $this->keterangan_pk])
            ->andFilterWhere(['like', 'keterangan_pk_p', $this->keterangan_pk_p])
            ->andFilterWhere(['like', 'analisis', $this->analisis]);

        return $dataProvider;
    }
}
